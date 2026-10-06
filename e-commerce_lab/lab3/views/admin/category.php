<?php

require_once __DIR__ . '/../../core/core.php';

require_admin();

require_once __DIR__ . '/../layout/header.php';


$editId = 0;


if (
    isset($_GET['edit_id']) &&
    filter_var(
        $_GET['edit_id'],
        FILTER_VALIDATE_INT
    )
) {

    $editId =
        (int) $_GET['edit_id'];
}

?>

<div class="brand-container">

    <h1>
        <?= $editId > 0
            ? 'Edit Category'
            : 'Add Category'
        ?>
    </h1>


    <div id="categoryMessage"></div>


    <form
        id="categoryForm"
        data-editing="<?= $editId > 0
            ? 'true'
            : 'false'
        ?>"
    >

        <?php if ($editId > 0): ?>

            <input
                type="hidden"
                name="cat_id"
                id="cat_id"
                value="<?= $editId ?>"
            >

        <?php endif; ?>


        <div class="form-group">

            <label for="cat_name">
                Category Name
            </label>

            <input
                type="text"
                id="cat_name"
                name="cat_name"
                placeholder="Enter category name"
                required
            >

        </div>


        <button
            type="submit"
            class="btn"
        >

            <?= $editId > 0
                ? 'Update Category'
                : 'Add Category'
            ?>

        </button>


        <?php if ($editId > 0): ?>

            <a
                href="<?= BASE_URL ?>/views/admin/category.php"
                class="form-link"
            >

                Cancel Edit

            </a>

        <?php endif; ?>

    </form>


    <h2 style="margin-top: 40px;">
        Existing Categories
    </h2>


    <div id="categoryTableContainer">

        <p>Loading categories...</p>

    </div>

</div>


<script>

    window.APP_BASE =
        <?= json_encode(BASE_URL) ?>;

    window.EDIT_CATEGORY_ID =
        <?= $editId ?>;

</script>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {


        loadCategories();


        const categoryForm =
            document.getElementById(
                "categoryForm"
            );


        categoryForm.addEventListener(
            "submit",
            handleCategorySubmit
        );


        if (
            window.EDIT_CATEGORY_ID > 0
        ) {

            loadCategoryForEditing(
                window.EDIT_CATEGORY_ID
            );

        }

        const categoryTableContainer =
            document.getElementById(
                "categoryTableContainer"
            );


        categoryTableContainer.addEventListener(
            "click",
            function (event) {

                const deleteButton =
                    event.target.closest(
                        ".delete-btn"
                    );


                if (!deleteButton) {

                    return;

                }


                const categoryId =
                    deleteButton.dataset.categoryId;


                if (!categoryId) {

                    return;

                }


                deleteCategory(
                    categoryId
                );

            }
        );

    }
);


function loadCategories()
{

    fetch(
        window.APP_BASE +
        "/api/category_api.php"
    )

    .then(response => {

        return response.json();

    })

    .then(data => {

        const container =
            document.getElementById(
                "categoryTableContainer"
            );


        if (!data.success) {

            container.innerHTML =
                `<div class="server-error">
                    ${escapeHtml(
                        data.error ||
                        "Unable to load categories."
                    )}
                 </div>`;

            return;
        }


        if (
            !data.categories ||
            data.categories.length === 0
        ) {

            container.innerHTML =
                "<p>No categories have been added yet.</p>";

            return;
        }


        let html = `

            <table class="brand-table">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Category Name
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

        `;


        data.categories.forEach(
            function (category) {

                html += `

                    <tr>

                        <td>
                            ${category.cat_id}
                        </td>

                        <td>
                            ${escapeHtml(
                                category.cat_name
                            )}
                        </td>

                        <td>

                            <a
                                class="edit-btn"
                                href="${
                                    window.APP_BASE
                                }/views/admin/category.php?edit_id=${
                                    category.cat_id
                                }"
                            >

                                Edit

                            </a>


                            <button
                                type="button"
                                class="delete-btn"
                                data-category-id="${
                                    category.cat_id
                                }"
                            >

                                Delete

                            </button>

                        </td>

                    </tr>

                `;

            }
        );


        html += `

                </tbody>

            </table>

        `;


        container.innerHTML =
            html;

    })

    .catch(error => {

        console.error(
            "LOAD CATEGORIES ERROR:",
            error
        );


        document.getElementById(
            "categoryTableContainer"
        ).innerHTML =

            `<div class="server-error">

                Unable to load categories.

             </div>`;

    });

}



function loadCategoryForEditing(id)
{

    fetch(
        window.APP_BASE +
        "/api/category_api.php?id=" +
        encodeURIComponent(id)
    )

    .then(response => {

        return response.json();

    })

    .then(data => {

        if (!data.success) {

            document.getElementById(
                "categoryMessage"
            ).innerHTML =

                `<div class="server-error">

                    ${escapeHtml(
                        data.error ||
                        "Unable to load category."
                    )}

                 </div>`;

            return;
        }


        document.getElementById(
            "cat_name"
        ).value =
            data.category.cat_name;

    })

    .catch(error => {

        console.error(
            "LOAD CATEGORY ERROR:",
            error
        );

    });

}


function handleCategorySubmit(event)
{

    event.preventDefault();


    const form =
        event.target;


    const name =
        document.getElementById(
            "cat_name"
        ).value.trim();


    const message =
        document.getElementById(
            "categoryMessage"
        );


    if (name === "") {

        message.innerHTML =
            `<div class="server-error">

                Category name is required.

             </div>`;

        return;
    }


    const formData =
        new FormData(form);


    let url =
        window.APP_BASE +
        "/actions/add_category_action.php";


    if (
        form.dataset.editing === "true"
    ) {

        url =
            window.APP_BASE +
            "/actions/update_category_action.php";

    }


    fetch(
        url,
        {
            method: "POST",
            body: formData
        }
    )

    .then(response => {

        return response.json();

    })

    .then(data => {

        if (data.success) {

            message.innerHTML =
                `<div class="success-message">

                    ${escapeHtml(
                        data.message ||
                        "Category saved successfully."
                    )}

                 </div>`;


            setTimeout(
                function () {

                    window.location.href =
                        window.APP_BASE +
                        "/views/admin/category.php";

                },
                700
            );

        } else {

            message.innerHTML =
                `<div class="server-error">

                    ${escapeHtml(
                        data.error ||
                        "Unable to save category."
                    )}

                 </div>`;

        }

    })

    .catch(error => {

        console.error(
            "SAVE CATEGORY ERROR:",
            error
        );


        message.innerHTML =
            `<div class="server-error">

                Something went wrong.

             </div>`;

    });

}



function deleteCategory(id)
{


    const confirmed =
        confirm(
            "Are you sure you want to delete this category?"
        );


    if (!confirmed) {

        return;

    }


    const formData =
        new FormData();


    formData.append(
        "cat_id",
        id
    );


    fetch(
        window.APP_BASE +
        "/actions/delete_category_action.php",
        {
            method: "POST",
            body: formData
        }
    )


    .then(response => {

        return response.text();

    })

    .then(text => {

        console.log(
            "DELETE CATEGORY RESPONSE:"
        );

        console.log(text);


   
        try {

            const data =
                JSON.parse(text);


            if (data.success) {

                alert(
                    data.message ||
                    "Category deleted successfully."
                );


            
                loadCategories();

            }

          
            else {

                alert(
                    data.error ||
                    "Unable to delete category."
                );

            }

        }

        catch (error) {

            console.error(
                "PHP RESPONSE:",
                text
            );


            console.error(
                "JSON ERROR:",
                error
            );


            alert(
                "The server returned an error. Check the browser console."
            );

        }

    })

    .catch(error => {

        console.error(
            "DELETE CATEGORY ERROR:",
            error
        );


        alert(
            "Something went wrong while deleting the category."
        );

    });

}


function escapeHtml(value)
{

    const div =
        document.createElement(
            "div"
        );


    div.textContent =
        value;


    return div.innerHTML;

}

</script>


<?php

require_once __DIR__ . '/../layout/footer.php';

?>
