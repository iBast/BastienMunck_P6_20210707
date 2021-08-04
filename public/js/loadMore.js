document.addEventListener("DOMContentLoaded", () => {

    loadmoreButton = document.getElementById("loadmore");
    updateLink(loadmoreButton);

    // When click on "Load more" button, load next page items in ajax just before the button
    loadmoreButton.addEventListener("click", function (event) {
        event.preventDefault();

        axios.get(this.getAttribute('href')).then(function (response) {
            this.parentElement.insertAdjacentHTML('beforebegin', response.data);
            updateLink(this);
        });
    });

    // Check the html content of the previous element and update or remove load more button
    function updateLink(this) {
        let parent = this.parentElement;
        let link = parent.previousElementSibling.innerHTML;
        if (link) {
            this.setAttribute('href', link);
        } else {
            parent.remove();
        }
    }

});