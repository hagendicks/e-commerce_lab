document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("productForm");

    if (!form) {
        return;
    }

    const MAX_IMAGE_BYTES = 2 * 1024 * 1024;
    const ALLOWED_TYPES = ["image/jpeg", "image/png", "image/gif", "image/webp"];

    const editing = form.dataset.editing === "true";
    const imageInput = document.getElementById("product_image");
    const preview = document.getElementById("imagePreview");
    const button = document.getElementById("productButton");
    const messageBox = document.getElementById("productMessage");


    function setError(id, text) {
        document.getElementById(id).textContent = text;
    }

    function clearErrors() {
        form.querySelectorAll(".error-message").forEach(function (el) {
            el.textContent = "";
        });
    }

    function showMessage(text, isError) {
        const div = document.createElement("div");
        div.className = isError ? "server-error" : "success-message";
        div.textContent = text;
        messageBox.innerHTML = "";
        messageBox.appendChild(div);
        messageBox.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }

    function parseJson(text) {
        try {
            return JSON.parse(text);
        } catch (e) {
            const start = text.indexOf("{");
            const end = text.lastIndexOf("}");
            if (start === -1 || end < start) {
                throw e;
            }
            return JSON.parse(text.slice(start, end + 1));
        }
    }


    function imageProblem(file) {
        if (!file) {
            return editing ? "" : "Please choose a product image.";
        }
        if (ALLOWED_TYPES.indexOf(file.type) === -1) {
            return "Only JPG, PNG, GIF or WEBP images are allowed.";
        }
        if (file.size > MAX_IMAGE_BYTES) {
            return "Image must be 2 MB or smaller.";
        }
        return "";
    }


    imageInput.addEventListener("change", function () {

        const file = imageInput.files[0];
        const problem = imageProblem(file);

        setError("imageError", file ? problem : "");

        if (file && !problem) {
            preview.src = URL.createObjectURL(file);
            preview.hidden = false;
        } else if (!editing) {
            preview.hidden = true;
        }
    });


    form.addEventListener("submit", function (event) {

        event.preventDefault();

        clearErrors();
        messageBox.innerHTML = "";

        const title = document.getElementById("product_title").value.trim();
        const cat = document.getElementById("product_cat").value;
        const brand = document.getElementById("product_brand").value;
        const price = document.getElementById("product_price").value;
        const desc = document.getElementById("product_desc").value.trim();
        const keywords = document.getElementById("product_keywords").value.trim();
        const file = imageInput.files[0];

        let valid = true;

        if (title === "") {
            setError("titleError", "Product title is required.");
            valid = false;
        } else if (title.length > 200) {
            setError("titleError", "Title must be 200 characters or fewer.");
            valid = false;
        }

        if (cat === "") {
            setError("catError", "Please choose a category.");
            valid = false;
        }

        if (brand === "") {
            setError("brandError", "Please choose a brand.");
            valid = false;
        }

        if (price === "" || isNaN(price) || parseFloat(price) <= 0) {
            setError("priceError", "Enter a price greater than 0.");
            valid = false;
        }

        if (desc.length > 500) {
            setError("descError", "Description must be 500 characters or fewer.");
            valid = false;
        }

        if (keywords.length > 100) {
            setError("keywordsError", "Keywords must be 100 characters or fewer.");
            valid = false;
        }

        const problem = imageProblem(file);

        if (problem) {
            setError("imageError", problem);
            valid = false;
        }

        if (!valid) {
            return;
        }


        const url = window.APP_BASE + (editing
            ? "/actions/update_product_action.php"
            : "/actions/add_product_action.php");

        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = editing ? "Updating..." : "Adding...";

        fetch(url, { method: "POST", body: new FormData(form) })

            .then(function (response) { return response.text(); })

            .then(function (text) {

                const data = parseJson(text);

                if (!data.success) {
                    showMessage(data.error || "Something went wrong.", true);
                    return;
                }

                showMessage(data.message || "Saved.", false);

                setTimeout(function () {
                    window.location.href = window.APP_BASE + "/views/admin/product.php";
                }, 900);
            })

            .catch(function (error) {
                console.error(error);
                showMessage("The server returned an unexpected response.", true);
            })

            .finally(function () {
                button.disabled = false;
                button.textContent = originalText;
            });
    });
        document.addEventListener("click", function (event) {

        const deleteButton = event.target.closest(".delete-btn");

        if (!deleteButton || !deleteButton.dataset.productId) {
            return;
        }

        if (!confirm("Are you sure you want to delete this product?")) {
            return;
        }

        const data = new FormData();
        data.append("product_id", deleteButton.dataset.productId);

        deleteButton.disabled = true;

        fetch(window.APP_BASE + "/actions/delete_product_action.php", {
            method: "POST",
            body: data
        })

            .then(function (response) { return response.text(); })

            .then(function (text) {

                const result = parseJson(text);

                if (!result.success) {
                    deleteButton.disabled = false;
                    showMessage(result.error || "Unable to delete the product.", true);
                    return;
                }

                showMessage(result.message || "Product deleted.", false);

                setTimeout(function () {
                    window.location.href = window.APP_BASE + "/views/admin/product.php";
                }, 700);
            })

            .catch(function (error) {
                console.error(error);
                deleteButton.disabled = false;
                showMessage("The server returned an unexpected response.", true);
            });
    });

});
