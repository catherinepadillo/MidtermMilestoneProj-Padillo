/* ==========================================================================
   Barangay Home Cooks - Client-side Interactive Application Logic
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {

    /* ----------------------------------------------------------------------
       1. AJAX Toggle Favorites (Feed & Detail Pages)
       ---------------------------------------------------------------------- */
    const favButtons = document.querySelectorAll('.fav-btn');

    favButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const recipeId = button.getAttribute('data-recipe-id');

            if (!recipeId) return;

            // Visual feedback during request
            const originalText = button.textContent;
            button.style.opacity = '0.6';
            button.disabled = true;

            try {
                const response = await fetch('toggle_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ recipe_id: parseInt(recipeId, 10) })
                });

                if (!response.ok) {
                    throw new Error(`HTTP Error: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    if (data.status === 'added') {
                        button.classList.add('active');
                        button.textContent = '❤️ Saved to Favorites';
                    } else {
                        button.classList.remove('active');
                        button.textContent = '🤍 Save to Favorites';
                    }
                } else {
                    alert(data.message || 'Unable to update favorite status.');
                    button.textContent = originalText;
                }
            } catch (err) {
                console.error('Error toggling favorite:', err);
                alert('A network error occurred while updating favorites. Please try again.');
                button.textContent = originalText;
            } finally {
                button.style.opacity = '1';
                button.disabled = false;
            }
        });
    });


    /* ----------------------------------------------------------------------
       2. Dynamic Ingredient Field Management (Create / Edit Recipe Forms)
       ---------------------------------------------------------------------- */
    const ingredientList = document.getElementById('ingredient-list');
    const addIngredientBtn = document.getElementById('add-ingredient-btn');

    if (ingredientList && addIngredientBtn) {
        addIngredientBtn.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'ingredient-row';
            row.style.cssText = 'display: flex; gap: 8px; margin-bottom: 8px; align-items: center;';

            row.innerHTML = `
                <input type="text" name="ingredients[]" placeholder="e.g., 1/2 kg Pork Belly, sliced" required style="flex: 1;">
                <button type="button" class="btn remove-ingredient-btn" style="background-color: #ef4444; padding: 6px 12px; font-size: 0.8rem;">✕</button>
            `;

            ingredientList.appendChild(row);

            // Focus the newly added input field
            row.querySelector('input').focus();
        });

        // Delegate removal event
        ingredientList.addEventListener('click', (e) => {
            if (e.target && e.target.classList.contains('remove-ingredient-btn')) {
                const rows = ingredientList.querySelectorAll('.ingredient-row');
                // Ensure at least one ingredient field remains
                if (rows.length > 1) {
                    e.target.closest('.ingredient-row').remove();
                } else {
                    alert('A recipe must have at least one ingredient.');
                }
            }
        });
    }


    /* ----------------------------------------------------------------------
       3. Global Confirmation Dialogs for Deletions
       ---------------------------------------------------------------------- */
    const deleteTriggers = document.querySelectorAll('.confirm-delete');

    deleteTriggers.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            const itemType = trigger.getAttribute('data-item-type') || 'item';
            const confirmed = confirm(`Sigurado ka ba? Are you sure you want to delete this ${itemType}? This action cannot be undone.`);
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });

});