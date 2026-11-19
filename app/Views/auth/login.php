<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1, viewport-fit=cover">

    <meta name="theme-color" content="#01696f">

    <title>Acceso · Laboratorio</title>

    <style>
        :root
        {
            color-scheme: light;

            --login-primary: #01696f;
            --login-primary-dark: #0c4e54;
            --login-background: #f0f5f5;
            --login-text: #213238;
            --login-muted: #596b71;
            --login-border: #bdcbd0;
        }

        *
        {
            box-sizing: border-box;
        }

        body
        {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;

            display: flex;
            flex-direction: column;

            background: var(--login-background);
            color: var(--login-text);

            font-family: system-ui, -apple-system, BlinkMacSystemFont,
                        "Segoe UI", sans-serif;
            font-size: 16px;
            line-height: 1.5;
        }

        .login-page
        {
            flex: 1;
            display: flex;
            flex-direction: column;

            width: 100%;
            padding: 1.25rem;

            padding-top: max(1.25rem, env(safe-area-inset-top));
            padding-bottom: max(1.25rem, env(safe-area-inset-bottom));
        }

        .login-box
        {
            width: 100%;
            max-width: 460px;
            margin: auto;
            padding: 2rem;

            background: #fff;
            border: 1px solid #dae4e6;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(20, 50, 55, .08);
        }

        .login-header
        {
            margin-bottom: 1.75rem;
        }

        .login-brand
        {
            margin: 0 0 .75rem;
            color: var(--login-primary);
            font-size: 1rem;
            font-weight: 700;
        }

        .login-title
        {
            margin: 0 0 .5rem;
            font-size: 1.65rem;
            line-height: 1.25;
        }

        .login-description
        {
            margin: 0;
            color: var(--login-muted);
        }

        .login-form
        {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .login-field
        {
            display: flex;
            flex-direction: column;
            gap: .45rem;
            min-width: 0;
        }

        .login-label
        {
            font-weight: 600;
        }

        .login-input
        {
            display: block;
            width: 100%;
            min-width: 0;
            min-height: 54px;
            margin: 0;
            padding: .8rem .9rem;

            border: 1px solid var(--login-border);
            border-radius: 8px;

            background: #fff;
            color: var(--login-text);

            font: inherit;
            font-size: 17px;
        }

        .login-input:focus
        {
            border-color: var(--login-primary);
            outline: 3px solid rgba(1, 105, 111, .2);
            outline-offset: 2px;
        }

        .login-show-password
        {
            display: flex;
            align-items: center;
            align-self: flex-start;
            gap: .65rem;

            min-height: 44px;
            cursor: pointer;
            color: var(--login-muted);
        }

        .login-show-password input
        {
            flex: 0 0 auto;
            width: 21px;
            height: 21px;
            margin: 0;

            accent-color: var(--login-primary);
            cursor: pointer;
        }

        .login-show-password input:focus-visible
        {
            outline: 3px solid rgba(1, 105, 111, .4);
            outline-offset: 3px;
        }

        .login-submit
        {
            width: 100%;
            min-height: 54px;
            padding: .85rem 1rem;

            border: 0;
            border-radius: 8px;

            background: var(--login-primary);
            color: #fff;

            font: inherit;
            font-size: 1.05rem;
            font-weight: 650;

            cursor: pointer;
            touch-action: manipulation;
        }

        .login-submit:hover
        {
            background: var(--login-primary-dark);
        }

        .login-submit:focus-visible
        {
            outline: 3px solid var(--login-text);
            outline-offset: 3px;
        }

        .login-error
        {
            margin-bottom: 1.5rem;
            padding: 1rem;

            border: 1px solid #efc3c3;
            border-radius: 8px;

            background: #fde8e8;
            color: #8a2424;

            font-size: .95rem;
            overflow-wrap: anywhere;
        }

        @media (max-width: 480px)
        {
            .login-page
            {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .login-box
            {
                padding: 1.5rem;
            }
        }

        @media (max-width: 360px)
        {
            .login-page
            {
                padding-left: .65rem;
                padding-right: .65rem;
            }

            .login-box
            {
                padding: 1.25rem;
            }
        }

        @media (max-height: 600px)
        {
            .login-box
            {
                margin-top: 0;
                margin-bottom: auto;
            }
        }
    </style>
</head>

<body>

    <main class="login-page">

        <section class="login-box"
                 aria-labelledby="login-title">

            <header class="login-header">
                <p class="login-brand">Laboratorio</p>

                <h1 id="login-title"
                    class="login-title">
                    Iniciar sesión
                </h1>

                <p class="login-description">
                    Introduce tu usuario y contraseña para acceder.
                </p>
            </header>

            <?php if (! empty($error)): ?>
                <div id="login-error"
                     class="login-error"
                     role="alert">
                    <?= esc($error) ?>
                </div>
            <?php endif ?>

            <form method="post"
                  action="<?= base_url() ?>"
                  class="login-form">

                <?= csrf_field() ?>

                <div class="login-field">
                    <label for="alias"
                           class="login-label">
                        Usuario
                    </label>

                    <input type="text"
                           id="alias"
                           name="alias"
                           class="login-input"
                           autocomplete="username"
                           autocapitalize="none"
                           spellcheck="false"
                           required>
                </div>

                <div class="login-field">
                    <label for="password"
                           class="login-label">
                        Contraseña
                    </label>

                    <input type="password"
                           id="password"
                           name="password"
                           class="login-input"
                           autocomplete="current-password"
                           required>
                </div>

                <label class="login-show-password"
                       for="show-password"
                       id="show-password-control"
                       hidden
                       style="display:none">

                    <input type="checkbox"
                           id="show-password"
                           aria-controls="password">

                    <span>Mostrar contraseña</span>
                </label>

                <button type="submit"
                        class="login-submit">
                    Entrar
                </button>

            </form>

        </section>
    </main>

    <script>
    const passwordInput = document.getElementById('password');
    const showPassword = document.getElementById('show-password');
    const showPasswordControl = document.getElementById(
        'show-password-control'
    );

    showPassword.addEventListener('change', function()
    {
        passwordInput.type = this.checked ? 'text' : 'password';
    });

    showPasswordControl.hidden = false;
    showPasswordControl.style.removeProperty('display');
    </script>

</body>
</html>