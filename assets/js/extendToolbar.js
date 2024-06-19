document.addEventListener('DOMContentLoaded', function () {
    var toolbar = document.querySelector('#Toolbar-listToolbar .toolbar-item.toolbar-primary > div');
    if (toolbar) {
        var button = document.createElement('a');
        button.href = '#';
        button.className = 'btn btn-primary oc-icon-clone disabled';
        button.innerHTML = 'Clone selected Post';
        button.id = 'cloneButton'; // Assign an id for easy access
        button.onclick = function(event) {
            event.preventDefault();
            if (!button.classList.contains('disabled')) {
                cloneSelectedPost();
            }
        };
        toolbar.appendChild(button);
    } else {
        console.error('Toolbar not found');
    }

    // Add event listener to check/uncheck posts and toggle the button visibility
    document.querySelectorAll('.control-list input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleCloneButton();
        });
    });

    function toggleCloneButton() {
        var button = document.getElementById('cloneButton');
        var checked = document.querySelectorAll('.control-list input[type="checkbox"]:checked');
        if (checked.length > 0) {
            button.classList.remove('disabled');
        } else {
            button.classList.add('disabled');
        }
    }

    function cloneSelectedPost() {
        var checked = document.querySelectorAll('.control-list input[type="checkbox"]:checked');
        if (checked.length > 0) {
            var postId = checked[0].value;
            $.request('onClonePost', {
                data: { postId: postId },
                success: function(data) {
                    window.location.href = data.editUrl;
                },
                error: function(error) {
                    console.error('Error cloning post:', error);
                }
            });
        }
    }
});
