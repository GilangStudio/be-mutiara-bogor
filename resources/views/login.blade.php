<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.2.0
* @link https://tabler.io
* Copyright 2018-2025 The Tabler Authors
* Copyright 2018-2025 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Login</title>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="{{ asset('css/tabler.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('icons/tabler-icons.min.css') }}" rel="stylesheet" />
    <!-- END GLOBAL MANDATORY STYLES -->


    <!-- BEGIN CUSTOM FONT -->
    <style>
        @import url("https://rsms.me/inter/inter.css");
    </style>
    <!-- END CUSTOM FONT -->

</head>

<body>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
            </div>
            <div class="card card-md">
                <form class="card-body" action="{{ route('login') }}" method="POST">
                    @csrf
                    <h1 class="mb-3 text-center">Login</h1>

                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <div class="alert-icon">
                            <i class="ti ti-exclamation-circle fs-2"></i>
                        </div>
                        <div>
                          <h4 class="alert-heading mb-0">{{ session('error') }}</h4>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" autocomplete="off" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group input-group-flat">
                            <input type="password" class="form-control" id="password" name="password" value="" autocomplete="off">
                            <span class="input-group-text">
                              <a class="input-group-link cursor-pointer" id="show-password-link">Show password</a>
                            </span>
                        </div>
                    </div>
                    <div class="my-4">
                        <button type="submit" class="btn btn-primary w-100 mt-4">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('js/tabler.min.js') }}" defer></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const showPasswordLink = document.getElementById('show-password-link');

            showPasswordLink.addEventListener('click', function (e) {
                e.preventDefault();
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    showPasswordLink.textContent = 'Hide password';
                } else {
                    passwordInput.type = 'password';
                    showPasswordLink.textContent = 'Show password';
                }
            });

            // Auto hide alert
            const alertElement = document.querySelector('.alert-dismissible');
            if (alertElement) {
                setTimeout(function() {
                    alertElement.style.display = 'none';
                }, 3000); // 3000 milliseconds = 3 seconds
            }
        });
    </script>

</body>

</html>