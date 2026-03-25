document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('input_fake');
    const alertContainer = document.getElementById('alert-container');

    if( input ) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                
                const div = document.createElement('div');
                div.className = 'alert alert-restricted';
                div.innerHTML = 'Une erreur a été rencontrée. Votre message n\'a pas pu être envoyé.<br/> L\'erreur a été signalée à l\'équipe technique. Réessayer plus tard.';
    
                alertContainer.appendChild(div);
            }
        });
    }
});