<x-guest-layout>
    <style>
        :root {
            --bg-deep: #f7f8fc;
            --bg-mid: #eef2f8;
            --bg-soft: #e7edf7;
            --card: rgba(255, 255, 255, 0.9);
            --card-border: rgba(30, 41, 59, 0.1);
            --input-bg: #ffffff;
            --text-main: #16233b;
            --text-muted: #55627c;
            --accent: #2f5fb7;
            --accent-hover: #3a6cc9;
            --danger: #c13737;
            --radius-lg: 16px;
            --radius-md: 13px;
        }

        .serene-login-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 20px;
            background:
                radial-gradient(700px 320px at 50% 10%, rgba(96, 137, 214, 0.24), transparent 70%),
                radial-gradient(540px 240px at 12% 88%, rgba(145, 177, 236, 0.24), transparent 72%),
                linear-gradient(145deg, #ffffff 0%, var(--bg-deep) 40%, var(--bg-mid) 72%, var(--bg-soft) 100%);
            font-family: "Manrope", "Segoe UI", sans-serif;
            color: var(--text-main);
        }

        .serene-login-wrap {
            width: min(94vw, 500px);
            display: grid;
            gap: 18px;
            justify-items: center;
        }

        .serene-logo-block {
            position: relative;
            border-radius: var(--radius-lg);
            background: #fbfcff;
            box-shadow: 0 22px 48px rgba(46, 71, 112, 0.18), 0 0 0 1px rgba(255, 255, 255, 0.92) inset;
            padding: 18px;
        }

        .serene-logo-block::before {
            content: "";
            position: absolute;
            inset: -14px;
            border-radius: 24px;
            background: radial-gradient(closest-side, rgba(115, 151, 216, 0.34), transparent);
            filter: blur(14px);
            z-index: -1;
        }

        .serene-logo {
            width: clamp(184px, 36vw, 250px);
            height: auto;
            object-fit: contain;
            display: block;
        }

        .serene-form-card {
            width: 100%;
            border-radius: var(--radius-lg);
            background: var(--card);
            border: 1px solid var(--card-border);
            box-shadow: 0 16px 38px rgba(32, 56, 96, 0.14);
            backdrop-filter: blur(9px);
            padding: 20px;
        }

        .serene-field {
            margin-top: 12px;
        }

        .serene-label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 600;
            color: #1f2e47;
        }

        .serene-input {
            width: 100%;
            border-radius: var(--radius-md);
            border: 1px solid rgba(72, 98, 140, 0.26);
            background: var(--input-bg);
            color: var(--text-main);
            padding: 12px 13px;
            font-size: 15px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .serene-input:hover {
            border-color: rgba(67, 103, 163, 0.45);
        }

        .serene-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(66, 104, 171, 0.18);
            background: #ffffff;
        }

        .serene-error {
            margin-top: 7px;
            color: var(--danger);
            font-size: 13px;
            line-height: 1.35;
        }

        .serene-meta {
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .serene-remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .serene-remember input {
            width: 15px;
            height: 15px;
            accent-color: var(--accent);
        }

        .serene-forgot {
            color: #325d9f;
            font-size: 14px;
            text-decoration: underline;
            text-underline-offset: 3px;
            transition: color 0.2s ease;
        }

        .serene-forgot:hover {
            color: #1f4c92;
        }

        .serene-actions {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }

        .serene-login-btn {
            border: none;
            border-radius: 13px;
            background: linear-gradient(135deg, #274f95, #1d3f7b);
            color: #f6f8ff;
            font-weight: 700;
            letter-spacing: 0.1em;
            font-size: 12px;
            padding: 11px 18px;
            min-width: 96px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .serene-login-btn:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(45, 77, 132, 0.28);
        }

        .serene-login-btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(45, 77, 132, 0.3);
        }

        .serene-status {
            width: 100%;
            margin-bottom: 10px;
            border-radius: 12px;
            background: rgba(89, 124, 183, 0.11);
            border: 1px solid rgba(89, 124, 183, 0.28);
            color: #294a7e;
            padding: 10px 12px;
            font-size: 14px;
        }

        @media (max-width: 560px) {
            .serene-form-card {
                padding: 18px 14px;
            }

            .serene-meta {
                align-items: flex-start;
                flex-direction: column;
            }

            .serene-actions {
                justify-content: stretch;
            }

            .serene-login-btn {
                width: 100%;
            }
        }
    </style>

    <div class="serene-login-page">
        <div class="serene-login-wrap">
            <a href="/" class="serene-logo-block" aria-label="Serene Home">
                <img src="{{ asset('img/logo-new.jpeg') }}" alt="Serene Logo" class="serene-logo">
            </a>

            <div class="serene-form-card">
                @if (session('status'))
                    <div class="serene-status">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="serene-field">
                        <label for="email" class="serene-label">Email</label>
                        <input id="email" class="serene-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                        @error('email')
                            <div class="serene-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="serene-field">
                        <label for="password" class="serene-label">Password</label>
                        <input id="password" class="serene-input" type="password" name="password" required autocomplete="current-password">
                        @error('password')
                            <div class="serene-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="serene-meta">
                        <label for="remember_me" class="serene-remember">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="serene-forgot" href="{{ route('password.request') }}">Forgot your password?</a>
                        @endif
                    </div>

                    <div class="serene-actions">
                        <button type="submit" class="serene-login-btn">LOG IN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
