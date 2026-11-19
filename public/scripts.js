// ============================================================
//  Indicaciones por determinación
//  Cada entrada puede tener 1 o varios tubos (array "tubos")
//  El tubo es siempre transparente; lo que identifica es el tapón.
// ============================================================
const INDICACIONES = {

    // ---------------- LCR ----------------
    lcr: {
        "lcr-bioq": {
            nombre: "Bioquímica",
            tubos: ["Tubo seco con tapón blanco"]
        },
        "lcr-micro": {
            nombre: "Microbiología",
            tubos: ["Tubo seco con tapón blanco"]
        },
        "lcr-deterioro": {
            nombre: "Deterioro cognitivo",
            tubos: ["Tubo específico"]
        },
        "lcr-recuento": {
            nombre: "Recuento celular",
            tubos: ["Tubo seco con tapón blanco"]
        }
    },

    // ---------------- SINOVIAL ----------------
    sinovial: {
        "sin-bioq": {
            nombre: "Bioquímica",
            tubos: [
                "Tubo seco con tapón blanco",
                "Tubo con tapón verde (heparina de sodio)"
            ]
        },
        "sin-recuento": {
            nombre: "Recuentos celulares",
            tubos: ["Tubo con tapón morado (EDTA)"]
        },
        "sin-micro": {
            nombre: "Microbiología",
            tubos: ["Tubo seco con tapón blanco"]
        },
        "sin-perfil": {
            nombre: "Perfil sinovial",
            tubos: [
                "Tubo con tapón verde (heparina de sodio)",
                "Tubo seco con tapón blanco",
                "Tubo con tapón morado (EDTA)"
            ]
        }
    },

    // ---------------- PLEURAL ----------------
    pleural: {
        "ple-ph": {
            nombre: "pH",
            tubos: ["Jeringa de gasometría"]
        },
        "ple-bioq": {
            nombre: "Bioquímica",
            tubos: [
                "Tubo seco con tapón blanco",
                "Tubo con tapón verde (heparina de sodio)"
            ]
        },
        "ple-recuento": {
            nombre: "Recuentos celulares",
            tubos: ["Tubo con tapón morado (EDTA)"]
        },
        "ple-micro": {
            nombre: "Microbiología",
            tubos: ["Tubo seco con tapón blanco"]
        }
    },

    // ---------------- ASCÍTICO ----------------
    ascitico: {
        "asc-bioq": {
            nombre: "Bioquímica",
            tubos: [
                "Tubo seco con tapón blanco",
                "Tubo con tapón verde (heparina de sodio)"
            ]
        },
        "asc-recuento": {
            nombre: "Recuentos celulares",
            tubos: ["Tubo con tapón morado (EDTA)"]
        },
        "asc-micro": {
            nombre: "Microbiología",
            tubos: ["Tubo seco con tapón blanco"]
        }
    },

    // ---------------- ABSCESO ----------------
    absceso: {
        "abs-micro": {
            nombre: "Microbiología",
            tubos: ["Tubo seco con tapón blanco. Si es muy abundante, recoger en contenedor estéril. Si es muy escaso, usar torunda con medio de transporte (tapón rosa)."]
        }
    }
};

// ============================================================
//  Exclusividad: "sin-perfil" desactiva los otros 3
// ============================================================
const EXCLUSIVOS = {
    "sin-perfil": ["sin-bioq", "sin-recuento", "sin-micro"]
};

// ============================================================
//  Navegación entre pasos
// ============================================================
function showStep(id) {
    document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
    const target = document.getElementById('step-' + id);
    if (target) target.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================================
//  Render de resultados
// ============================================================
function renderResult(tipo) {
    const contenedor = document.getElementById('result-' + tipo);
    const contenido = document.getElementById('result-' + tipo + '-content');
    if (!contenedor || !contenido) return;

    const seleccionados = document.querySelectorAll(
        `#step-${tipo} .checkbox-btn.selected`
    );

    if (seleccionados.length === 0) {
        contenedor.classList.remove('active');
        contenido.innerHTML = '';
        return;
    }

    let html = '';
    seleccionados.forEach(btn => {
        const clave = btn.dataset.check;
        const datos = INDICACIONES[tipo]?.[clave];
        if (!datos) return;

        // Cada tubo se lista por separado para dejar claro
        // que se necesitan varios tubos si hay varios.
        const listaTubos = datos.tubos
            .map(t => `• ${t}`)
            .join('<br>');

        html += `
            <p>
                <strong>${datos.nombre}</strong><br>
                ${listaTubos}
            </p>
        `;
    });

    contenido.innerHTML = html;
    contenedor.classList.add('active');

    // Desplazar hasta las opciones del paso activo
    const stepActivo = document.getElementById('step-' + tipo);
    const bloqueOpciones = stepActivo?.querySelector('.options');
    if (bloqueOpciones) {
        bloqueOpciones.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// ============================================================
//  Aplicar exclusividades (perfil sinovial)
// ============================================================
function aplicarExclusividad(claveMarcada, tipo) {
    // ¿La clave marcada excluye a otras?
    if (EXCLUSIVOS[claveMarcada]) {
        EXCLUSIVOS[claveMarcada].forEach(otra => {
            const btn = document.querySelector(
                `#step-${tipo} .checkbox-btn[data-check="${otra}"]`
            );
            if (btn) {
                btn.classList.remove('selected');
                btn.disabled = true;
            }
        });
    }

    // ¿La clave marcada es excluida por alguna que esté activa?
    for (const principal in EXCLUSIVOS) {
        if (EXCLUSIVOS[principal].includes(claveMarcada)) {
            const btnPrincipal = document.querySelector(
                `#step-${tipo} .checkbox-btn[data-check="${principal}"]`
            );
            if (btnPrincipal && btnPrincipal.classList.contains('selected')) {
                // Ya está activo el principal: no permitir marcar este
                return false;
            }
        }
    }
    return true;
}

// ============================================================
//  Rehabilitar botones al desmarcar el "perfil"
// ============================================================
function rehabilitarExcluidos(tipo) {
    for (const principal in EXCLUSIVOS) {
        const btnPrincipal = document.querySelector(
            `#step-${tipo} .checkbox-btn[data-check="${principal}"]`
        );
        if (btnPrincipal && !btnPrincipal.classList.contains('selected')) {
            EXCLUSIVOS[principal].forEach(otra => {
                const btn = document.querySelector(
                    `#step-${tipo} .checkbox-btn[data-check="${otra}"]`
                );
                if (btn) btn.disabled = false;
            });
        }
    }
}

// ============================================================
//  Eventos
// ============================================================
document.addEventListener('click', (e) => {

    // 1. Selección de tipo de líquido
    if (e.target.matches('.option-btn[data-tipo]')) {
        showStep(e.target.dataset.tipo);
        return;
    }

    // 2. Botones tipo checkbox
    if (e.target.matches('.checkbox-btn')) {
        const btn = e.target;
        const step = btn.closest('.step');
        const tipo = step.id.replace('step-', '');
        const clave = btn.dataset.check;

        // Si vamos a marcar (no estaba seleccionado)
        if (!btn.classList.contains('selected')) {
            const permitido = aplicarExclusividad(clave, tipo);
            if (!permitido) return;
            btn.classList.add('selected');
        } else {
            btn.classList.remove('selected');
            // Si era el perfil, rehabilitamos los otros
            if (EXCLUSIVOS[clave]) {
                rehabilitarExcluidos(tipo);
            }
        }

        renderResult(tipo);
        return;
    }

    // 3. Atrás
    if (e.target.matches('.back-btn')) {
        document.querySelectorAll('.checkbox-btn.selected').forEach(b => b.classList.remove('selected'));
        document.querySelectorAll('.checkbox-btn').forEach(b => b.disabled = false);
        document.querySelectorAll('.result').forEach(r => r.classList.remove('active'));
        showStep(e.target.dataset.back);
        return;
    }

    // 4. Reiniciar
    if (e.target.matches('.restart-btn')) {
        document.querySelectorAll('.checkbox-btn.selected').forEach(b => b.classList.remove('selected'));
        document.querySelectorAll('.checkbox-btn').forEach(b => b.disabled = false);
        document.querySelectorAll('.result').forEach(r => r.classList.remove('active'));
        showStep('inicio');
        return;
    }
});