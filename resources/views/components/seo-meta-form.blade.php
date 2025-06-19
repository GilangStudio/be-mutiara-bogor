{{-- SEO Meta --}}
@if ($type == 'create')
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-seo me-2"></i>
            SEO Meta (Opsional)
        </h3>
        <div class="card-actions">
            <span class="badge bg-blue-lt">
                <i class="ti ti-info-circle me-1"></i>
                Digunakan untuk optimasi mesin pencari
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Meta Title</label>
                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                           name="meta_title" value="{{ old('meta_title') }}" 
                           placeholder="Enter title that will appear in search results"
                           maxlength="255" id="meta-title-input">
                    @error('meta_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-hint">
                        <span id="meta-title-count">0</span>/255 characters. 
                    </small>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Meta Description</label>
                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                              name="meta_description" rows="3" 
                              placeholder="Enter description that will appear in search results"
                              maxlength="500" id="meta-desc-input">{{ old('meta_description') }}</textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-hint">
                        <span id="meta-desc-count">0</span>/500 characters. 
                    </small>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                           name="meta_keywords" value="{{ old('meta_keywords') }}" 
                           placeholder="keywords separated by commas. e.g: property, house, Jakarta, residence"
                           maxlength="255" id="meta-keywords-input">
                    @error('meta_keywords')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-hint">
                        <span id="meta-keywords-count">0</span>/255 characters. 
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@elseif ($type == 'edit')
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-seo me-2"></i>
            SEO Meta (Opsional)
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Meta Title</label>
                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                           name="meta_title" value="{{ old('meta_title', $data->meta_title) }}" 
                           placeholder="Judul yang akan muncul di hasil pencarian Google"
                           maxlength="255" id="meta-title-input">
                    @error('meta_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-hint">
                        <span id="meta-title-count">{{ strlen($data->meta_title ?? '') }}</span>/255 characters. 
                    </small>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Meta Description</label>
                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                              name="meta_description" rows="3" 
                              placeholder="Deskripsi yang akan muncul di hasil pencarian Google"
                              maxlength="500" id="meta-desc-input">{{ old('meta_description', $data->meta_description) }}</textarea>
                    @error('meta_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-hint">
                        <span id="meta-desc-count">{{ strlen($data->meta_description ?? '') }}</span>/500 characters. 
                    </small>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                           name="meta_keywords" value="{{ old('meta_keywords', $data->meta_keywords) }}" 
                           placeholder="kata kunci, properti, perumahan, Jakarta"
                           maxlength="255" id="meta-keywords-input">
                    @error('meta_keywords')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-hint">
                        <span id="meta-keywords-count">{{ strlen($data->meta_keywords ?? '') }}</span>/255 characters. 
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">SEO Meta</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label text-secondary">Meta Title</label>
                    <div>{{ $data->meta_title_display }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label text-secondary">Meta Description</label>
                    <div style="max-height: 100px">{!! $data->meta_description_display !!}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label text-secondary">Meta Keywords</label>
                    <div>{{ $data->meta_keywords_display }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    function setupCharacterCounter(inputId, countId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(countId);
        
        if (input && counter) {
            input.addEventListener('input', function() {
                const currentLength = this.value.length;
                counter.textContent = currentLength;
                
                // Add warning colors
                const percentage = (currentLength / maxLength) * 100;
                if (percentage > 90) {
                    counter.parentElement.classList.add('text-danger');
                    counter.parentElement.classList.remove('text-warning');
                } else if (percentage > 80) {
                    counter.parentElement.classList.add('text-warning');
                    counter.parentElement.classList.remove('text-danger');
                } else {
                    counter.parentElement.classList.remove('text-warning', 'text-danger');
                }
            });
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character counters for meta fields
        setupCharacterCounter('meta-title-input', 'meta-title-count', 255);
        setupCharacterCounter('meta-desc-input', 'meta-desc-count', 500);
        setupCharacterCounter('meta-keywords-input', 'meta-keywords-count', 255);
    });
</script>
@endpush
        