<?php
/**
 * Formulario de edición del perfil del usuario conectado.
 *
 * Variables:
 * - $perfil
 * - $errores
 */

$exito = session()->getFlashdata('perfil_exito');
?>

<section class="perfil-page">

    <header class="perfil-header">
        <h1>Mi perfil</h1>
        <p>Modifica tus datos y, si lo necesitas, tu contraseña.</p>
    </header>

    <?php if ($exito): ?>
        <div class="perfil-message perfil-message--success"
             role="status">
            <?= esc($exito) ?>
        </div>
    <?php endif ?>

    <?php if (! empty($errores)): ?>
        <div class="perfil-message perfil-message--error"
             role="alert">
            <p>No se han guardado los cambios:</p>

            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <form id="perfil-form"
          method="post"
          action="<?= site_url('mi-perfil/guardar') ?>"
          class="perfil-form">

        <?= csrf_field() ?>

        <div class="perfil-field">
            <label for="perfil-nombre">Nombre</label>

            <input type="text"
                   id="perfil-nombre"
                   name="nombre"
                   value="<?= esc($perfil['nombre'], 'attr') ?>"
                   maxlength="256"
                   autocomplete="name"
                   required>
        </div>

        <div class="perfil-field">
            <label for="perfil-alias">Nombre de usuario</label>

            <input type="text"
                   id="perfil-alias"
                   name="alias"
                   value="<?= esc($perfil['alias'], 'attr') ?>"
                   maxlength="16"
                   autocomplete="username"
                   autocapitalize="none"
                   spellcheck="false"
                   aria-describedby="perfil-alias-status"
                   required>

            <p id="perfil-alias-status"
               class="perfil-hint"
               role="status"
               aria-live="polite"></p>
        </div>

        <div class="perfil-field">
            <label for="perfil-password-actual">
                Contraseña actual
            </label>

            <input type="password"
                   id="perfil-password-actual"
                   name="password_actual"
                   autocomplete="current-password"
                   maxlength="1024"
                   required>

            <p class="perfil-hint">
                Necesaria para confirmar los cambios.
            </p>
        </div>

        <fieldset class="perfil-password-group">

            <legend>Cambiar contraseña</legend>

            <p class="perfil-hint">
                Deja los dos campos vacíos para conservar la actual.
            </p>

            <div class="perfil-field">
                <label for="perfil-password-nueva">
                    Nueva contraseña
                </label>

                <input type="password"
                       id="perfil-password-nueva"
                       name="password_nueva"
                       autocomplete="new-password"
                       maxlength="1024">
            </div>

            <div class="perfil-field">
                <label for="perfil-password-repetir">
                    Repetir nueva contraseña
                </label>

                <input type="password"
                       id="perfil-password-repetir"
                       name="password_repetir"
                       autocomplete="new-password"
                       maxlength="1024">

                <p id="perfil-password-status"
                   class="perfil-hint"
                   role="status"
                   aria-live="polite"></p>
            </div>

        </fieldset>

        <button type="submit"
                id="perfil-guardar"
                class="perfil-submit">
            Guardar
        </button>

    </form>
</section>

<style>
    .perfil-page
    {
        width: 100%;
        max-width: 620px;
    }

    .perfil-header
    {
        margin-bottom: 1.5rem;
    }

    .perfil-header h1
    {
        margin: 0 0 .5rem;
        font-size: 1.5rem;
    }

    .perfil-header p
    {
        margin: 0;
        color: #596b71;
    }

    .perfil-form
    {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;

        padding: 1.5rem;
        background: #fff;
        border: 1px solid #d7e1e3;
        border-radius: 10px;
    }

    .perfil-field
    {
        display: flex;
        flex-direction: column;
        gap: .45rem;
        min-width: 0;
    }

    .perfil-field label
    {
        font-weight: 600;
    }

    .perfil-field input
    {
        width: 100%;
        min-width: 0;
        min-height: 50px;
        padding: .75rem;
        box-sizing: border-box;

        border: 1px solid #bdcbd0;
        border-radius: 6px;
        background: #fff;
        color: #213238;

        font: inherit;
        font-size: 16px;
    }

    .perfil-field input:focus
    {
        outline: 3px solid rgba(1, 105, 111, .2);
        outline-offset: 2px;
        border-color: #01696f;
    }

    .perfil-field input[aria-invalid="true"]
    {
        border-color: #a12c2c;
    }

    .perfil-hint
    {
        margin: 0;
        color: #596b71;
        font-size: .9rem;
    }

    .perfil-hint.is-error
    {
        color: #a12c2c;
    }

    .perfil-hint.is-success
    {
        color: #205c36;
    }

    .perfil-password-group
    {
        display: flex;
        flex-direction: column;
        gap: 1rem;

        min-width: 0;
        margin: 0;
        padding: 1rem;
        border: 1px solid #d7e1e3;
        border-radius: 8px;
    }

    .perfil-password-group legend
    {
        padding: 0 .4rem;
        font-weight: 600;
    }

    .perfil-submit
    {
        width: 100%;
        min-height: 52px;
        padding: .8rem 1rem;

        border: 0;
        border-radius: 6px;
        background: #01696f;
        color: #fff;

        font: inherit;
        font-weight: 600;
        cursor: pointer;
    }

    .perfil-submit:disabled
    {
        opacity: .6;
        cursor: not-allowed;
    }

    .perfil-message
    {
        margin-bottom: 1.25rem;
        padding: 1rem;
        border-radius: 8px;
    }

    .perfil-message p
    {
        margin: 0;
    }

    .perfil-message ul
    {
        margin: .5rem 0 0;
        padding-left: 1.25rem;
    }

    .perfil-message--success
    {
        background: #e7f4ec;
        color: #205c36;
    }

    .perfil-message--error
    {
        background: #fde8e8;
        color: #8a2424;
    }

    @media (max-width: 480px)
    {
        .perfil-form
        {
            padding: 1rem;
        }
    }
</style>

<script>
    (() =>
    {
        const form = document.getElementById('perfil-form');
        const alias = document.getElementById('perfil-alias');
        const aliasStatus = document.getElementById('perfil-alias-status');
        const nueva = document.getElementById('perfil-password-nueva');
        const repetir = document.getElementById('perfil-password-repetir');
        const passwordStatus = document.getElementById('perfil-password-status');

        const aliasUrl = <?= json_encode(
            site_url('mi-perfil/alias'),
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        ) ?>;

        let temporizador;
        let peticion = null;
        let version = 0;

        function mensajeAlias(texto, estado = '')
        {
            aliasStatus.textContent = texto;
            aliasStatus.className = 'perfil-hint';

            if (estado)
            {
                aliasStatus.classList.add(estado);
            }
        }

        alias.addEventListener('input', () =>
        {
            clearTimeout(temporizador);

            version++;
            const versionActual = version;

            if (peticion)
            {
                peticion.abort();
            }

            alias.setCustomValidity('');
            alias.removeAttribute('aria-invalid');

            const valor = alias.value.trim();

            if (!valor)
            {
                mensajeAlias('El nombre de usuario es obligatorio.', 'is-error');
                return;
            }

            mensajeAlias('Comprobando disponibilidad…');

            temporizador = setTimeout(async () =>
            {
                const abortController = new AbortController();
                peticion = abortController;

                try
                {
                    const url = new URL(aliasUrl, window.location.href);
                    url.searchParams.set('alias', valor);

                    const res = await fetch(url,
                    {
                        headers:
                        {
                            'Accept': 'application/json'
                        },
                        cache: 'no-store',
                        signal: abortController.signal
                    });

                    const contentType = res.headers.get('content-type') ?? '';

                    if (!res.ok || !contentType.includes('application/json'))
                    {
                        throw new Error('Respuesta no válida.');
                    }

                    const json = await res.json();

                    if (versionActual !== version)
                    {
                        return;
                    }

                    const disponible = json.disponible === true;

                    mensajeAlias(
                        json.mensaje,
                        disponible ? 'is-success' : 'is-error'
                    );

                    alias.setAttribute(
                        'aria-invalid',
                        disponible ? 'false' : 'true'
                    );

                    alias.setCustomValidity(
                        disponible ? '' : json.mensaje
                    );
                }
                catch (error)
                {
                    if (
                        error.name === 'AbortError'
                        || versionActual !== version
                    )
                    {
                        return;
                    }

                    mensajeAlias(
                        'No se ha podido comprobar. Se validará al guardar.'
                    );
                }
            }, 400);
        });

        function comprobarPasswords()
        {
            nueva.setCustomValidity('');
            repetir.setCustomValidity('');
            passwordStatus.textContent = '';
            passwordStatus.className = 'perfil-hint';

            if (nueva.value === '' && repetir.value === '')
            {
                return;
            }

            if (nueva.value === '')
            {
                nueva.setCustomValidity('Introduce la nueva contraseña.');
                return;
            }

            if (nueva.value !== repetir.value)
            {
                const mensaje = 'Las nuevas contraseñas no coinciden.';

                repetir.setCustomValidity(mensaje);
                passwordStatus.textContent = mensaje;
                passwordStatus.classList.add('is-error');
            }
            else
            {
                passwordStatus.textContent = 'Las contraseñas coinciden.';
                passwordStatus.classList.add('is-success');
            }
        }

        nueva.addEventListener('input', comprobarPasswords);
        repetir.addEventListener('input', comprobarPasswords);

        form.addEventListener('submit', event =>
        {
            comprobarPasswords();

            if (!form.reportValidity())
            {
                event.preventDefault();
            }
        });
    })();
</script>