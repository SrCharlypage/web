<?php
// footer.php
?>
    <!-- Footer común -->
    <footer class="page-footer fade-in-up" style="animation-delay:0.7s; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);
	border-radius:16px; color: #8b5a3c; padding:2rem; text-align:center; box-shadow:0 8px 25px rgba(0,0,0,0.1); border:1px solid rgba(255,255,255,0.2); margin-top:2rem;">
        <p class="footer-quote">
            "Venerable Hermandad del Santísimo Cristo de Humildad y Paciencia, María Santísima de las Penas, San Juan Evangelista y Santa María Magdalena"
        </p>
        <p class="footer-year">Sistema de Gestión © <?php echo date('Y'); ?></p>
    </footer>
</main>

<!-- Scripts comunes -->
<script>
    // Función de logout
    function logout() {
        if (confirm('¿Está seguro que desea cerrar sesión?')) {
            window.location.href = 'logout.php';
        }
    }

    // Animaciones y lógica común
    document.addEventListener('DOMContentLoaded', function() {
        // Efecto de entrada animada para tarjetas
        const cards = document.querySelectorAll('.option-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(50px)';
            card.style.transition = 'all 0.8s ease';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 200);
        });

        // Prevenir doble clic en botones con clase .action-button
        const buttons = document.querySelectorAll('.action-button');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                this.disabled = true;
                setTimeout(() => { this.disabled = false; }, 1000);
            });
        });

        // Teclas rápidas: Ctrl+F y Escape
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                window.location.href = 'buscar_hermanos.php';
            }
            if (e.key === 'Escape') {
                window.location.href = 'index.php';
            }
        });

        console.log('Footer scripts cargados correctamente');
    });
</script>

</body>
</html>
