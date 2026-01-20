@extends('layouts.app')

@section('title', 'Настройки магазина - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Настройки магазина</h1>
    
    <style>
        .settings-form {
            max-width: 1000px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .form-grid .full-width {
                grid-column: 1 / -1;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.875rem;
        }

        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-group .help-text {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .form-group .error {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .form-group .toggle-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toggle-switch {
            position: relative;
            width: 52px;
            height: 28px;
            background: #d1d5db;
            border-radius: 9999px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-switch.active {
            background: #2563eb;
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .toggle-switch.active::after {
            transform: translateX(24px);
        }

        .toggle-switch input {
            display: none;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .settings-section {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .settings-section h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        @media (max-width: 767px) {
            .settings-form {
                padding: 1rem;
            }

            .form-grid {
                gap: 1rem;
            }
        }
    </style>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.settings.store') }}" method="POST" class="settings-form">
        @csrf
        
        <div class="settings-section">
            <h2>Настройки Робокассы</h2>
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="merchant_login">Логин магазина *</label>
                    <input type="text" name="merchant_login" id="merchant_login" value="{{ old('merchant_login', $settings['merchant_login']) }}" required>
                    <span class="help-text">Логин вашего магазина в системе Робокасса</span>
                    @error('merchant_login') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full-width">
                    <label class="toggle-wrapper">
                        <span style="font-weight: 500; color: #374151; font-size: 0.875rem;">Тестовый режим</span>
                        <div class="toggle-switch {{ old('is_test', $settings['is_test']) ? 'active' : '' }}" onclick="this.classList.toggle('active'); document.getElementById('is_test').value = this.classList.contains('active') ? '1' : '0';">
                            <input type="hidden" name="is_test" id="is_test" value="{{ old('is_test', $settings['is_test'] ? '1' : '0') }}">
                        </div>
                    </label>
                    <span class="help-text">Включите для использования тестовых паролей, выключите для рабочих паролей</span>
                    @error('is_test') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password1_test">Пароль #1 (Тестовый)</label>
                    <input type="password" name="password1_test" id="password1_test" value="" placeholder="{{ $settings['password1_test'] ? '●●●●●●●●' : 'Введите пароль' }}">
                    <input type="hidden" name="password1_test_old" value="{{ $settings['password1_test'] }}">
                    <span class="help-text">Пароль для создания платежей в тестовом режиме. Оставьте пустым, чтобы не изменять.</span>
                    @error('password1_test') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password1_production">Пароль #1 (Рабочий)</label>
                    <input type="password" name="password1_production" id="password1_production" value="" placeholder="{{ $settings['password1_production'] ? '●●●●●●●●' : 'Введите пароль' }}">
                    <input type="hidden" name="password1_production_old" value="{{ $settings['password1_production'] }}">
                    <span class="help-text">Пароль для создания платежей в рабочем режиме. Оставьте пустым, чтобы не изменять.</span>
                    @error('password1_production') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password2_test">Пароль #2 (Тестовый)</label>
                    <input type="password" name="password2_test" id="password2_test" value="" placeholder="{{ $settings['password2_test'] ? '●●●●●●●●' : 'Введите пароль' }}">
                    <input type="hidden" name="password2_test_old" value="{{ $settings['password2_test'] }}">
                    <span class="help-text">Пароль для проверки уведомлений в тестовом режиме. Оставьте пустым, чтобы не изменять.</span>
                    @error('password2_test') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="password2_production">Пароль #2 (Рабочий)</label>
                    <input type="password" name="password2_production" id="password2_production" value="" placeholder="{{ $settings['password2_production'] ? '●●●●●●●●' : 'Введите пароль' }}">
                    <input type="hidden" name="password2_production_old" value="{{ $settings['password2_production'] }}">
                    <span class="help-text">Пароль для проверки уведомлений в рабочем режиме. Оставьте пустым, чтобы не изменять.</span>
                    @error('password2_production') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="hash_type">Тип хеширования *</label>
                    <select name="hash_type" id="hash_type" required>
                        <option value="md5" {{ old('hash_type', $settings['hash_type']) === 'md5' ? 'selected' : '' }}>MD5</option>
                        <option value="sha256" {{ old('hash_type', $settings['hash_type']) === 'sha256' ? 'selected' : '' }}>SHA256</option>
                        <option value="sha512" {{ old('hash_type', $settings['hash_type']) === 'sha512' ? 'selected' : '' }}>SHA512</option>
                    </select>
                    <span class="help-text">Алгоритм хеширования для подписи запросов</span>
                    @error('hash_type') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить настройки</button>
        </div>
    </form>
</div>
@endsection
