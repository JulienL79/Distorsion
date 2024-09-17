document.addEventListener('DOMContentLoaded', () => {
    
    const popup = document.getElementById('custom-confirm-popup');
    const confirmButton = document.getElementById('confirm');
    const cancelButton = document.getElementById('cancel');
    
    document.querySelectorAll('.delete-btn').forEach(button => {
        
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Empêche l'action par défaut (ex. redirection)
            
            const url = this.getAttribute('href'); // Récupère l'URL du bouton "delete"
            
            // Affiche la popup
            popup.style.display = 'flex';
    
            // Gère le clic sur le bouton "Confirmer"
            confirmButton.onclick = function() {
                window.location.href = url;
            };
    
            // Gère le clic sur le bouton "Annuler"
            cancelButton.onclick = function() {
                popup.style.display = 'none';
            };
        });
    });
});