<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use App\Models\Sales;
use App\Enum\LeadStatus;
use App\Models\Platform;
use App\Models\HistoryLeads;
use Illuminate\Http\Request;
use App\Services\LeadsService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\LeadsStatisticsService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\NotificationFirebaseService;

class LeadsController extends Controller
{
    protected $statisticsService;
    protected $leadsService;
    protected $notificationFirebaseService;

    public function __construct(LeadsStatisticsService $statisticsService, LeadsService $leadsService, NotificationFirebaseService $notificationFirebaseService)
    {
        $this->statisticsService = $statisticsService;
        $this->leadsService = $leadsService;
        $this->notificationFirebaseService = $notificationFirebaseService;
    }
    
    /**
     * Mendapatkan daftar leads untuk sales yang sedang login
     * dengan pagination, filter, dan informasi statistik status leads
     */
    public function get_leads(Request $request)
    {
        try {
            // Validasi input filter
            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string|max:255',
                // 'status' => 'nullable|in:' . implode(',', array_column(LeadStatus::cases(), 'value')),
                'platform_id' => 'nullable|exists:platforms,id',
                'assignment_type' => 'nullable|in:auto,manual',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'is_favorited' => 'nullable|in:true,false',
                'has_recontact' => 'nullable|in:true,false',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Parameter filter tidak valid',
                    'data' => [
                        'errors' => $validator->errors()
                    ]
                ], 422);
            }

            // Mendapatkan sales ID dari bearer token
            $sales = Sales::where('api_token', request()->bearerToken())->whereNotNull('api_token')->active()->first();
            
            if (!$sales) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token tidak valid atau sales tidak ditemukan',
                    'data' => null
                ], 401);
            }

            $salesId = $sales->id;

            // Build query dengan filter
            $query = Lead::select([
                    'id', 
                    'name', 
                    'phone', 
                    'email', 
                    'message', 
                    'status as leads_status', 
                    'note as leads_note', 
                    'platform_id', 
                    'path_referral',
                    'recontact_count',
                    'last_contact_at',
                    'created_at'
                ])
                ->whereHas('historyLead', function ($q) use ($salesId) {
                    $q->where('sales_id', $salesId);
                })
                ->with([
                    'platform:id,platform_name',
                    'historyLead:leads_id,is_favorited,is_automatic'
                ]);

            // Apply search filter
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('phone', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('message', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('note', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('path_referral', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', strtoupper($request->status));
            }

            // Apply platform filter
            if ($request->filled('platform_id')) {
                $query->where('platform_id', $request->platform_id);
            }

            // Apply assignment type filter
            if ($request->filled('assignment_type')) {
                $assignmentType = $request->assignment_type === 'auto';
                $query->whereHas('historyLead', function($q) use ($assignmentType) {
                    $q->where('is_automatic', $assignmentType);
                });
            }

            // Apply favorite filter
            if ($request->filled('is_favorited')) {
                $query->whereHas('historyLead', function($q) use ($request) {
                    $q->where('is_favorited', $request->boolean('is_favorited'));
                });
            }

            // Apply follow up filter
            if ($request->filled('has_recontact')) {
                if ($request->boolean('has_recontact')) {
                    $query->where('recontact_count', '>', 0);
                } else {
                    $query->where('recontact_count', '=', 0);
                }
            }

            // Apply date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Get statistics for all filtered leads (without pagination)
            $allFilteredLeads = clone $query;
            $statusCounts = $allFilteredLeads->select('status')
                ->groupBy('status')
                ->selectRaw('status, count(*) as count')
                ->pluck('count', 'status');

            $totalLeadsStatus = [
                'new' => $statusCounts->get(LeadStatus::NEW->value, 0),
                'process' => $statusCounts->get(LeadStatus::PROCESS->value, 0),
                'closing' => $statusCounts->get(LeadStatus::CLOSING->value, 0)
            ];

            // Get recontact statistics
            $recontactStats = [
                'total_with_recontact' => (clone $allFilteredLeads)->where('recontact_count', '>', 0)->count(),
                'total_without_recontact' => (clone $allFilteredLeads)->where('recontact_count', '=', 0)->count(),
                'recent_recontacts' => (clone $allFilteredLeads)->where('last_contact_at', '>=', now()->subDays(7))->where('recontact_count', '>', 0)->count()
            ];

            // Apply pagination
            $perPage = $request->get('per_page', 15);
            $leads = $query
                // Priority order: Recontact dengan last_contact_at terbaru di atas, lalu berdasarkan created_at
                // ->orderByRaw('CASE WHEN recontact_count > 0 THEN 0 ELSE 1 END') // Recontact first
                ->orderBy('last_contact_at', 'desc') // Latest contact first
                ->orderBy('created_at', 'desc') // Then by creation date
                ->paginate($perPage)
                ->appends($request->query());

            // Transform paginated data
            $transformedLeads = $leads->getCollection()->map(function ($lead) {
                $isRecontact = $lead->recontact_count > 0;
                $isRecentRecontact = $isRecontact && $lead->last_contact_at && $lead->last_contact_at->isAfter(now()->subHours(24));

                return [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'phone' => $lead->phone,
                    'email' => $lead->email,
                    'message' => $lead->message,
                    'leads_status' => $lead->leads_status,
                    'leads_note' => $lead->leads_note,
                    'path_referral' => $lead->path_referral,
                    'platform' => $lead->platform,
                    'is_favorited' => $lead->historyLead->is_favorited ?? false,
                    'assignment_type' => $lead->historyLead->is_automatic ? 'auto' : 'manual',
                    'recontact_info' => [
                        'is_recontact' => $isRecontact,
                        'recontact_count' => $lead->recontact_count,
                        'is_recent_recontact' => $isRecentRecontact,
                        'last_contact_at' => $lead->last_contact_at ? $lead->last_contact_at->toISOString() : null,
                        'last_contact_formatted' => $lead->last_contact_at ? $lead->last_contact_at->format('d M Y, H:i') : null,
                        'last_contact_diff' => $lead->last_contact_at ? $lead->last_contact_at->diffForHumans() : null
                    ],
                    'date' => $lead->created_at->format('H:i, d F Y'),
                    'created_at' => $lead->created_at->toISOString(),
                    'formatted_phone' => $this->formatPhoneNumber($lead->phone),
                    'whatsapp_url' => $this->getWhatsAppUrl($lead->phone)
                ];
            });

            // Set transformed collection back to paginator
            $leads->setCollection($transformedLeads);

            return response()->json([
                'status' => 'success',
                'message' => 'Leads berhasil diambil',
                'data' => [
                    'leads' => [
                        'current_page' => $leads->currentPage(),
                        'data' => $leads->items(),
                        'first_page_url' => $leads->url(1),
                        'from' => $leads->firstItem(),
                        'last_page' => $leads->lastPage(),
                        'last_page_url' => $leads->url($leads->lastPage()),
                        'next_page_url' => $leads->nextPageUrl(),
                        'path' => $leads->path(),
                        'per_page' => $leads->perPage(),
                        'prev_page_url' => $leads->previousPageUrl(),
                        'to' => $leads->lastItem(),
                        'total' => $leads->total()
                    ],
                    'total_leads_status' => $totalLeadsStatus,
                    'recontact_stats' => $recontactStats,
                    'filters_applied' => [
                        'search' => $request->search,
                        'status' => $request->status,
                        'platform_id' => $request->platform_id,
                        'assignment_type' => $request->assignment_type,
                        'is_favorited' => $request->is_favorited,
                        'has_recontact' => $request->has_recontact,
                        'date_from' => $request->date_from,
                        'date_to' => $request->date_to
                    ],
                    'summary' => [
                        'total_filtered' => $leads->total(),
                        'current_page_count' => $leads->count(),
                        'has_more_pages' => $leads->hasMorePages(),
                        'priority_info' => 'Recent recontacts are prioritized at the top'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data leads',
                'data' => null
            ], 500);
        }
    }

    /**
     * Update data lead
     * Hanya bisa update leads yang dimiliki oleh sales tersebut
     */
    public function update_lead(Request $request, $leadId)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|min:10|max:15|regex:/^([0-9\s\-\+\(\)]*)$/',
                'email' => 'nullable|email|max:255',
                'message' => 'nullable|string|max:1000',
                'note' => 'nullable|string|max:500',
                'path_referral' => 'nullable|string|max:255'
            ], [
                'name.required' => 'Nama wajib diisi',
                'name.max' => 'Nama maksimal 255 karakter',
                'phone.required' => 'Nomor telepon wajib diisi',
                'phone.min' => 'Nomor telepon minimal 10 digit',
                'phone.max' => 'Nomor telepon maksimal 15 karakter',
                'phone.regex' => 'Format nomor telepon tidak valid',
                'email.email' => 'Format email tidak valid',
                'email.max' => 'Email maksimal 255 karakter',
                'message.max' => 'Pesan maksimal 1000 karakter',
                'note.max' => 'Catatan maksimal 500 karakter',
                'path_referral.max' => 'Path referral maksimal 255 karakter'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak valid',
                    'data' => [
                        'errors' => $validator->errors()
                    ]
                ], 422);
            }

            // Validasi lead ID
            if (!is_numeric($leadId) || $leadId <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID lead tidak valid',
                    'data' => null
                ], 400);
            }

            // Mendapatkan sales ID dari bearer token
            $sales = Sales::where('api_token', request()->bearerToken())->whereNotNull('api_token')->active()->first();
            
            if (!$sales) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token tidak valid atau sales tidak ditemukan',
                    'data' => null
                ], 401);
            }

            $salesId = $sales->id;

            // Cari lead dengan validasi kepemilikan
            $lead = Lead::where('id', $leadId)
                       ->whereHas('historyLead', function ($query) use ($salesId) {
                           $query->where('sales_id', $salesId);
                       })
                       ->first();

            if (!$lead) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lead tidak ditemukan atau Anda tidak memiliki akses',
                    'data' => null
                ], 404);
            }

            // Validasi nomor telepon unik (kecuali untuk lead ini sendiri)
            $phoneExists = Lead::where('phone', $request->phone)
                              ->where('id', '!=', $leadId)
                              ->exists();

            if ($phoneExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nomor telepon sudah terdaftar untuk lead lain',
                    'data' => null
                ], 422);
            }

            // Validasi email unik jika diisi (kecuali untuk lead ini sendiri)
            if ($request->filled('email')) {
                $emailExists = Lead::where('email', $request->email)
                                  ->where('id', '!=', $leadId)
                                  ->exists();

                if ($emailExists) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Email sudah terdaftar untuk lead lain',
                        'data' => null
                    ], 422);
                }
            }

            // Update data lead
            $lead->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'message' => $request->message,
                'note' => $request->note,
                'path_referral' => $request->path_referral,
                'updated_by' => $sales->user_id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data lead berhasil diupdate',
                'data' => [
                    'lead' => [
                        'id' => $lead->id,
                        'name' => $lead->name,
                        'phone' => $lead->phone,
                        'email' => $lead->email,
                        'message' => $lead->message,
                        'note' => $lead->note,
                        'path_referral' => $lead->path_referral,
                        'updated_at' => $lead->updated_at->toISOString()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate lead',
                'data' => null
            ], 500);
        }
    }

    /**
     * Toggle status favorite lead
     */
    public function toggle_favorite(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'lead_id' => 'required|integer|min:1',
                'is_favorited' => 'required|boolean'
            ], [
                'lead_id.required' => 'ID lead wajib diisi',
                'lead_id.integer' => 'ID lead harus berupa angka',
                'lead_id.min' => 'ID lead tidak valid',
                'is_favorited.required' => 'Status favorite wajib diisi',
                'is_favorited.boolean' => 'Status favorite harus berupa true/false'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak valid',
                    'data' => [
                        'errors' => $validator->errors()
                    ]
                ], 422);
            }

            // Mendapatkan sales ID dari bearer token
            $sales = Sales::where('api_token', request()->bearerToken())->whereNotNull('api_token')->active()->first();
            
            if (!$sales) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token tidak valid atau sales tidak ditemukan',
                    'data' => null
                ], 401);
            }

            $salesId = $sales->id;
            $leadId = $request->lead_id;

            // Validasi lead dan kepemilikan
            $lead = Lead::where('id', $leadId)
                       ->whereHas('historyLead', function ($query) use ($salesId) {
                           $query->where('sales_id', $salesId);
                       })
                       ->first();

            if (!$lead) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lead tidak ditemukan atau Anda tidak memiliki akses',
                    'data' => null
                ], 404);
            }

            // Update status favorite di history lead
            $historyLead = $lead->historyLead;
            $historyLead->update([
                'is_favorited' => $request->boolean('is_favorited')
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $request->boolean('is_favorited') 
                    ? 'Lead berhasil ditambahkan ke favorite' 
                    : 'Lead berhasil dihapus dari favorite',
                'data' => [
                    'lead' => [
                        'id' => $lead->id,
                        'name' => $lead->name,
                        'is_favorited' => $request->boolean('is_favorited')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate status favorite',
                'data' => null
            ], 500);
        }
    }

    /**
     * Change status lead
     */
    public function change_status(Request $request, $leadId)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:' . strtolower(implode(',', array_column(LeadStatus::cases(), 'value'))),
            ], [
                'status.required' => 'Status wajib diisi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak valid',
                    'data' => [
                        'errors' => $validator->errors()
                    ]
                ], 422);
            }

            // Validasi lead ID
            if (!is_numeric($leadId) || $leadId <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID lead tidak valid',
                    'data' => null
                ], 400);
            }

            // Mendapatkan sales ID dari bearer token
            $sales = Sales::where('api_token', request()->bearerToken())->whereNotNull('api_token')->active()->first();
            
            if (!$sales) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token tidak valid atau sales tidak ditemukan',
                    'data' => null
                ], 401);
            }

            $salesId = $sales->id;

            // Cari lead dengan validasi kepemilikan
            $lead = Lead::where('id', $leadId)
                       ->whereHas('historyLead', function ($query) use ($salesId) {
                           $query->where('sales_id', $salesId);
                       })
                       ->first();

            if (!$lead) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lead tidak ditemukan atau Anda tidak memiliki akses',
                    'data' => null
                ], 404);
            }

            $oldStatus = $lead->status;
            $newStatus = strtoupper($request->status);

            // Validasi perubahan status
            if ($oldStatus === $newStatus) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Status lead sudah sama dengan yang dipilih',
                    'data' => null
                ], 422);
            }

            // Update status dan note jika ada
            $updateData = [
                'status' => $newStatus,
                'updated_by' => $sales->user_id
            ];

            $lead->update($updateData);

            // Log aktivitas perubahan status (opsional - bisa ditambahkan ke tabel log)
            Log::info("Lead status changed", [
                'lead_id' => $lead->id,
                'sales_id' => $salesId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Status lead berhasil diubah dari {$oldStatus} ke {$newStatus}",
                'data' => [
                    'lead' => [
                        'id' => $lead->id,
                        'name' => $lead->name,
                        'status' => $lead->status,
                        'note' => $lead->note,
                        'updated_at' => $lead->updated_at->toISOString(),
                        'status_history' => [
                            'old_status' => $oldStatus,
                            'new_status' => $newStatus,
                            'changed_by' => $sales->name,
                            'changed_at' => $lead->updated_at->format('d M Y, H:i')
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengubah status lead',
                'data' => null
            ], 500);
        }
    }

    /**
     * Mendapatkan statistik leads untuk dashboard sales
     */
    public function get_statistics(Request $request)
    {
        try {
            // Validasi input untuk filter tanggal
            $validator = Validator::make($request->all(), [
                'period' => 'nullable|in:today,week,month,quarter,year,custom',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'platform_id' => 'nullable|exists:platforms,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Parameter tidak valid',
                    'data' => [
                        'errors' => $validator->errors()
                    ]
                ], 422);
            }

            // Mendapatkan sales ID dari bearer token
            $sales = Sales::where('api_token', request()->bearerToken())->whereNotNull('api_token')->active()->first();
            
            if (!$sales) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token tidak valid atau sales tidak ditemukan',
                    'data' => null
                ], 401);
            }

            // Prepare options untuk service
            $options = [
                'period' => $request->get('period', 'month'),
                'platform_id' => $request->platform_id,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to
            ];

            // Dapatkan statistik dari service
            $statistics = $this->statisticsService->getDashboardStats($sales->id, $options);

            // Tambahkan info sales
            $statistics['sales_info'] = [
                'id' => $sales->id,
                'name' => $sales->name,
                'email' => $sales->email,
                'phone' => $sales->phone,
                'order' => $sales->order
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Statistik leads berhasil diambil',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil statistik',
                'error' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Format nomor telepon untuk WhatsApp
     */
    private function formatPhoneNumber($phone)
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Format Indonesian phone number
        if (substr($phone, 0, 1) === '0') {
            return '+62' . substr($phone, 1);
        }
        
        return $phone;
    }

    /**
     * Generate WhatsApp URL
     */
    private function getWhatsAppUrl($phone)
    {
        if (!$phone) {
            return null;
        }

        $formattedPhone = $this->formatPhoneNumber($phone);
        return "https://wa.me/" . str_replace('+', '', $formattedPhone);
    }

    /**
     * Mendapatkan detail lead berdasarkan ID
     * Hanya bisa mengakses leads yang dimiliki oleh sales tersebut
     */
    public function get_leads_detail($leadId)
    {
        try {
            // Validasi lead ID
            if (!is_numeric($leadId) || $leadId <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID lead tidak valid',
                    'data' => null
                ], 400);
            }

            // Mendapatkan sales ID dari bearer token
            $sales = Sales::where('api_token', request()->bearerToken())->whereNotNull('api_token')->active()->first();
            
            if (!$sales) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token tidak valid atau sales tidak ditemukan',
                    'data' => null
                ], 401);
            }

            $salesId = $sales->id;

            // Query lead dengan validasi kepemilikan
            $lead = Lead::select([
                    'id',
                    'name',
                    'phone',
                    'email',
                    'message',
                    'status as lead_status',
                    'note',
                    'platform_id',
                    'path_referral',
                    'recontact_count',
                    'last_contact_at',
                    'created_at',
                    'updated_at'
                ])
                ->where('id', $leadId)
                ->whereHas('historyLead', function ($query) use ($salesId) {
                    $query->where('sales_id', $salesId);
                })
                ->with([
                    'platform:id,platform_name',
                    'historyLead:leads_id,sales_id,is_favorited,is_automatic,created_at',
                    'historyLead.sales:id,name,phone,email',
                    'historyMoveLeads' => function($query) {
                        $query->orderBy('created_at', 'desc')
                              ->with([
                                  'fromSales:id,name',
                                  'toSales:id,name'
                              ]);
                    },
                    'createdBy:id,name',
                    'updatedBy:id,name'
                ])
                ->first();

            if (!$lead) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lead tidak ditemukan atau Anda tidak memiliki akses',
                    'data' => null
                ], 404);
            }

            $isRecontact = $lead->recontact_count > 0;
            $isRecentRecontact = false;
            
            if ($isRecontact && $lead->last_contact_at) {
                $isRecentRecontact = $lead->last_contact_at->isAfter(now()->subHours(24));
            }

            // Transform data detail
            $leadDetail = [
                'id' => $lead->id,
                'name' => $lead->name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'message' => $lead->message,
                'lead_status' => $lead->lead_status,
                'note' => $lead->note,
                'path_referral' => $lead->path_referral,
                'platform' => [
                    'id' => $lead->platform->id,
                    'name' => $lead->platform->platform_name
                ],
                'assignment_info' => [
                    'type' => $lead->historyLead->is_automatic ? 'auto' : 'manual',
                    'type_text' => $lead->historyLead->is_automatic ? 'Otomatis' : 'Manual',
                    'assigned_at' => $lead->historyLead->created_at->format('d M Y, H:i'),
                    'assigned_by' => [
                        'id' => $lead->historyLead->sales->id,
                        'name' => $lead->historyLead->sales->name,
                        'phone' => $lead->historyLead->sales->phone,
                        'email' => $lead->historyLead->sales->email
                    ]
                ],
                'is_favorited' => $lead->historyLead->is_favorited ?? false,
                'recontact_info' => [
                    'is_recontact' => $isRecontact,
                    'recontact_count' => $lead->recontact_count,
                    'is_recent_recontact' => $isRecentRecontact,
                    'last_contact_at' => $lead->last_contact_at ? $lead->last_contact_at->toISOString() : null,
                    'last_contact_formatted' => $lead->last_contact_at ? $lead->last_contact_at->format('d M Y, H:i') : null,
                    'last_contact_diff' => $lead->last_contact_at ? $lead->last_contact_at->diffForHumans() : null,
                    'recontact_label' => $lead->recontact_count === 0 ? 'New Lead' : 
                                    ($lead->recontact_count === 1 ? 'Recontact' : "Recontact ({$lead->recontact_count}x)")
                ],
                'contact_info' => [
                    'formatted_phone' => $this->formatPhoneNumber($lead->phone),
                    'whatsapp_url' => $this->getWhatsAppUrl($lead->phone),
                    'email_url' => $lead->email ? "mailto:{$lead->email}" : null
                ],
                'movement_history' => $lead->historyMoveLeads->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'from_sales' => [
                            'id' => $history->fromSales->id,
                            'name' => $history->fromSales->name
                        ],
                        'to_sales' => [
                            'id' => $history->toSales->id,
                            'name' => $history->toSales->name
                        ],
                        'moved_at' => $history->created_at->format('d M Y, H:i'),
                        'moved_at_diff' => $history->created_at->diffForHumans()
                    ];
                }),
                'timeline' => $this->buildTimeline($lead),
                'metadata' => [
                    'created_at' => $lead->created_at->toISOString(),
                    'created_at_formatted' => $lead->created_at->format('d M Y, H:i'),
                    'created_by' => $lead->createdBy ? [
                        'id' => $lead->createdBy->id,
                        'name' => $lead->createdBy->name
                    ] : null,
                    'updated_at' => $lead->updated_at->toISOString(),
                    'updated_at_formatted' => $lead->updated_at->format('d M Y, H:i'),
                    'updated_by' => $lead->updatedBy ? [
                        'id' => $lead->updatedBy->id,
                        'name' => $lead->updatedBy->name
                    ] : null,
                    'created_diff' => $lead->created_at->diffForHumans(),
                    'updated_diff' => $lead->updated_at->diffForHumans()
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Detail lead berhasil diambil',
                'data' => [
                    'lead' => $leadDetail
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil detail lead',
                'data' => null
            ], 500);
        }
    }

    /**
     * Build timeline untuk lead
     */
    private function buildTimeline($lead)
    {
        $timeline = [];

        // Initial assignment
        if ($lead->historyLead) {
            $timeline[] = [
                'type' => 'assignment',
                'title' => 'Lead Ditugaskan',
                'description' => "Ditugaskan ke {$lead->historyLead->sales->name}",
                'assignment_type' => $lead->historyLead->is_automatic ? 'Otomatis' : 'Manual',
                'date' => $lead->historyLead->created_at->format('d M Y, H:i'),
                'date_diff' => $lead->historyLead->created_at->diffForHumans(),
                'icon' => 'user-plus',
                'color' => 'blue'
            ];
        }

        // Movement history
        foreach ($lead->historyMoveLeads as $history) {
            $timeline[] = [
                'type' => 'movement',
                'title' => 'Perpindahan Sales',
                'description' => "Dipindahkan dari {$history->fromSales->name} ke {$history->toSales->name}",
                'from_sales' => $history->fromSales->name,
                'to_sales' => $history->toSales->name,
                'date' => $history->created_at->format('d M Y, H:i'),
                'date_diff' => $history->created_at->diffForHumans(),
                'icon' => 'arrow-right',
                'color' => 'orange'
            ];
        }

        // Sort timeline by date descending
        usort($timeline, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $timeline;
    }

    // public function store(Request $request) {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'phone' => [
    //             'required',
    //             'min:10',
    //             'max:13',
    //             'regex:/^([0-9\s\-\+\(\)]*)$/',
    //             'not_regex:/^0{3,}/',
    //             'starts_with:0',
    //             Rule::unique('leads', 'phone')
    //         ],
    //         'email' => [
    //             'nullable',
    //             'email',
    //             Rule::unique('leads')->where(function ($query) {
    //                 return $query->whereNotNull('email');
    //             }),
    //         ],
    //         'message' => 'nullable|string',
    //     ], [
    //         'phone.unique' => 'Nomor Anda sudah terdaftar. Silahkan hubungi kami kembali via whatsapp. Terimakasih',  // Pesan kustom untuk validasi phone unique
    //         'email.unique' => 'Email Anda sudah terdaftar. Silahkan hubungi kami kembali via whatsapp. Terimakasih'
    //     ]);
    
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $validator->errors()->first()
    //         ], 422);
    //     }
    //     $referer = null;

    //     if ($request->header('referer')) {
    //         $referer = $request->header('referer');
    //     }

    //     //otomatis cari sales yang paling akhir
    //     // $sales_id 
    //     $max_sales_sort = Sales::max('sales_sort');
    //     if (!$max_sales_sort) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'No sales found',
    //         ]);
    //     }
    //     $last_sales_from_history_leads = HistoryLeads::with('sales:id,sales_sort')->where('is_automatic', true)->orderBy('id', 'desc')->first();
        
    //     //only for first time input
    //     if (is_null($last_sales_from_history_leads)) {
    //         //get sales with first sales_sort
    //         $sales_id = Sales::where('sales_sort', 1)->first()->id;
    //     }
    //     else {
    //         $next_sales = $last_sales_from_history_leads->sales->sales_sort + 1;
            
    //         // if ($max_sales_sort == $last_sales_from_history_leads->sales->sales_sort) {
    //         if ($next_sales > $max_sales_sort) {
    //             //get sales with first sales_sort
    //             $sales_id = Sales::select('id')->where('sales_sort', 1)->first()->id;
    //         }
    //         else {
    //             $sales_id = Sales::where('sales_sort', $next_sales)->first()->id;
    //         }
    //     }

    //     $platform_id = 1;
    //     $platform = Platform::where('id', $platform_id)->first();

    //     if (!$platform) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Platform not found'
    //         ]);
    //     }

    //     $lead = new Lead();
    //     $lead->name = $request->name;
    //     $lead->mobile = $request->phone;
    //     $lead->email = $request->email;
    //     $lead->message = $request->message;
    //     $lead->platform_id = $platform_id;
    //     $lead->path_referral = $referer;
    //     $lead->save();

    //     HistoryLeads::create([
    //         'leads_id' => $lead->id,
    //         'sales_id' => $sales_id,
    //         'is_automatic' => true
    //     ]);

    //     $sales = Sales::where('id', $sales_id)->first();

    //     event(new NotificationFirebase([
    //         'title' => 'Leads Baru',
    //         'body' => 'Hai ' . strtoupper($sales->sales_name) . ', ada leads baru dari ' . strtoupper($lead->name) . '. Segera follow up.',
    //         'fcm_token' => $sales->fcm_token
    //     ]));

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Thanks for your interest in our project. We will contact you soon.'
    //     ]);
    // }

    /**
     * Store a newly created lead from website form
     * Dengan automatic sales assignment berurutan
     */
    public function store(Request $request)
    {
        // Rate limiting to prevent spam
        $key = 'leads-api:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'status' => 'error',
                'message' => "Terlalu banyak percobaan. Silakan coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'phone' => 'required|min:10|max:15|regex:/^([0-9\s\-\+\(\)]*)$/|not_regex:/^0{3,}/|starts_with:0',
            'email' => 'nullable|email|max:255',
            'message' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.min' => 'Nama minimal 2 karakter',
            'name.max' => 'Nama maksimal 255 karakter',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.min' => 'Nomor telepon minimal 10 digit',
            'phone.max' => 'Nomor telepon maksimal 15 karakter',
            'phone.regex' => 'Format nomor telepon tidak valid',
            'phone.not_regex' => 'Format nomor telepon tidak valid',
            'phone.starts_with' => 'Format nomor telepon tidak valid',
            // 'phone.unique' => 'Nomor Anda sudah terdaftar. Silahkan hubungi kami kembali via whatsapp. Terimakasih',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            // 'email.unique' => 'Email ini sudah terdaftar',
            'message.max' => 'Pesan maksimal 1000 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {

            // Check if lead with this phone already exists
            $existingLead = Lead::where('phone', $request->phone)->first();

            if ($existingLead) {
                // Handle re-submission
                return $this->handleReSubmission($request, $existingLead);
            }


            // Get referer URL
            $referer = null;
            if ($request->header('referer')) {
                $referer = $request->header('referer');
            }

            $platform_id = 1;
            $platform = Platform::where('id', $platform_id)->first();
            if (!$platform) {
                return response()->json([
                   'status' => 'error',
                   'message' => 'Platform not found'
                ]);
            }


            // Get platform (default to website platform atau first platform)
            // $platform_id = $request->platform_id;
            // if (!$platform_id) {
            //     // Cari platform 'Website' atau gunakan platform pertama
            //     $platform = Platform::where('platform_name', 'like', '%website%')
            //                       ->orWhere('platform_name', 'like', '%web%')
            //                       ->first();
                
            //     if (!$platform) {
            //         $platform = Platform::orderBy('id')->first();
            //     }
                
            //     if (!$platform) {
            //         return response()->json([
            //             'status' => 'error',
            //             'message' => 'Platform tidak tersedia. Silakan hubungi administrator.'
            //         ], 500);
            //     }
                
            //     $platform_id = $platform->id;
            // }

            // Automatic sales assignment - rotasi berurutan
            $sales_id = $this->leadsService->getNextSalesId();

            if (!$sales_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada sales yang tersedia saat ini. Silakan coba lagi nanti atau hubungi kami langsung.'
                ], 503);
            }

            // Sanitize input data
            $data = [
                'name' => strip_tags(trim($request->name)),
                'phone' => $request->phone,
                'email' => $request->email ? strtolower(trim($request->email)) : null,
                'message' => $request->message ? strip_tags(trim($request->message)) : null,
                'status' => LeadStatus::NEW->value,
                'platform_id' => $platform_id,
                'path_referral' => $referer,
                'note' => null,
                'recontact_count' => 0,
                'last_contact_at' => now()
            ];

            // Create lead
            $lead = Lead::create($data);

            // Create history lead record (automatic assignment)
            HistoryLeads::create([
                'leads_id' => $lead->id,
                'sales_id' => $sales_id,
                'is_automatic' => true,
                'is_favorited' => false
            ]);

            // Get sales info for notification
            $sales = Sales::find($sales_id);

            // TODO: Send notification to sales
            // Contoh implementasi notification (bisa pakai Firebase, email, dll)
            if ($sales && $sales->fcm_token) {
                $this->notificationFirebaseService->sendNewLeadNotification($sales, $lead);
                // event(new NotificationFirebase([
                //     'title' => 'Lead Baru',
                //     'body' => 'Hai ' . strtoupper($sales->name) . ', ada lead baru dari ' . strtoupper($lead->name) . '. Segera follow up.',
                //     'fcm_token' => $sales->fcm_token
                // ]));
            }

            // Hit rate limiter
            RateLimiter::hit($key, 300); // 5 minutes decay

            // Log successful lead creation
            Log::info('New lead created via API', [
                'lead_id' => $lead->id,
                'lead_name' => $lead->name,
                'lead_phone' => $lead->phone,
                'assigned_sales_id' => $sales_id,
                'assigned_sales_name' => $sales->name ?? 'Unknown',
                'platform_id' => $platform_id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $referer
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Terima kasih atas minat Anda terhadap proyek kami. Tim kami akan segera menghubungi Anda.'
            ]);

        } catch (\Exception $e) {
            Log::error('Lead creation API failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'ip_address' => $request->ip(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Maaf, terjadi kesalahan saat mengirim data. Silakan coba lagi atau hubungi kami langsung.',
            ], 500);
        }
    }

    /**
     * Handle re-submission dari lead yang sudah ada
     */
    private function handleReSubmission(Request $request, Lead $existingLead)
    {
        try {
            // Update lead data with new information
            $updateData = [
                // 'name' => strip_tags(trim($request->name)), // Update name if changed
                'last_contact_at' => now(), // Update last contact time
                'recontact_count' => $existingLead->recontact_count + 1 // Increment recontact count
            ];

            // Update message if provided
            if ($request->message) {
                $newMessage = strip_tags(trim($request->message));
                
                // Append new message to existing if different
                if ($existingLead->message && $existingLead->message !== $newMessage) {
                    $updateData['message'] = $existingLead->message . "\n\n--- Recontact " . ($existingLead->recontact_count + 1) . " (" . now()->format('d M Y H:i') . ") ---\n" . $newMessage;
                } elseif (!$existingLead->message) {
                    $updateData['message'] = $newMessage;
                }
            }

            // Update platform if different
            if ($request->platform_id && $request->platform_id !== $existingLead->platform_id) {
                $updateData['platform_id'] = $request->platform_id;
            }

            // Update path referral
            if ($request->header('referer')) {
                $updateData['path_referral'] = $request->header('referer');
            }

            // Update the lead
            $existingLead->update($updateData);

            // Get current sales from history
            $currentSales = $existingLead->historyLead->sales ?? null;

            // Send notification to current sales about recontact
            if ($currentSales && $currentSales->fcm_token) {
                $this->notificationService->sendRecontactNotification($currentSales, $existingLead);
            }

            // Log recontact submission
            Log::info('Lead recontact submission via API', [
                'lead_id' => $existingLead->id,
                'lead_name' => $existingLead->name,
                'lead_phone' => $existingLead->phone,
                'recontact_count' => $existingLead->recontact_count,
                'assigned_sales_id' => $currentSales->id ?? null,
                'assigned_sales_name' => $currentSales->name ?? 'Unknown',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'is_recontact' => true
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Terima kasih telah menghubungi kami kembali. Tim kami akan segera memproses permintaan terbaru Anda.',
            ]);

        } catch (\Exception $e) {
            Log::error('Lead recontact submission failed: ' . $e->getMessage(), [
                'existing_lead_id' => $existingLead->id,
                'request_data' => request()->all(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Maaf, terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi atau hubungi kami langsung.',
            ], 500);
        }
    }
}