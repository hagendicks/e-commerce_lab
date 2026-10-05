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
        <?= $editId > 0 ? 'Edit Brand' : 'Add Brand' ?>
    </h1>


    <div id="brandMessage"></div>


    <form
        id="brandForm"
        data-editing="<?= $editId > 0 ? 'true' : 'false' ?>"
    >

        <?php if ($editId > 0): ?>

            <input
                type="hidden"
                name="brand_id"
                id="brand_id"
                value="<?= $editId ?>"
            >

        <?php endif; ?>


        <div class="form-group">

            <label for="brand_name">
                Brand Name
            </label>

            <input
                type="text"
                id="brand_name"
                name="brand_name"
                placeholder="Enter brand name"
                required
            >

        </div>


        <button
            type="submit"
            class="btn">

            <?= $editId > 0
                ? 'Update Brand'
                : 'Add Brand'
            ?>

        </button>


        <?php if ($editId > 0): ?>

            <a
                href="<?= BASE_URL ?>/views/admin/brand.php"
                class="form-link">

                Cancel Edit

            </a>

        <?php endif; ?>

    </form>


    <h2 style="margin-top: 40px;">
        Existing Brands
    </h2>


    <div id="brandTableContainer">

        <p>Loading brands...</p>

    </div>

</div>


<script>

    window.APP_BASE =
        <?= json_encode(BASE_URL) ?>;

    window.EDIT_BRAND_ID =
        <?= $editId ?>;

</script>


<script src="<?= BASE_URL ?>/js/validate.js"></script>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        loadBrands();


        document
            .getElementById("brandTableContainer")
            .addEventListener(
                "click",
                function (event) {

                    const deleteButton =
                        event.target.closest(".delete-btn");


                    if (!deleteButton) {
                        return;
                    }


                    const brandId =
                        deleteButton.dataset.brandId;


                    if (!brandId) {
                        return;
                    }


                    deleteBrand(brandId);

                }
            );


        if (window.EDIT_BRAND_ID > 0) {

            loadBrandForEditing(
                window.EDIT_BRAND_ID
            );

        }

    }
);


function parseJsonResponse(text)
{

    try {

        return JSON.parse(text);

    }
    catch (error) {

        const start =
            text.indexOf("{");

        const end =
            text.lastIndexOf("}");


        if (
            start === -1 ||
            end === -1 ||
            end < start
        ) {
            throw error;
        }


        return JSON.parse(
            text.slice(
                start,
                end + 1
            )
        );

    }

}


function loadBrands()
{

    fetch(
        window.APP_BASE +
        "/api/brand_api.php"
    )

    .then(response => response.text())

    .then(text => {

        const data =
            parseJsonResponse(text);


        const container =
            document.getElementById(
                "brandTableContainer"
            );


        if (!data.success) {

            container.innerHTML =
                `<div class="server-error">
                    ${escapeHtml(data.error || "Unable to load brands.")}
                 </div>`;

            return;
        }


        if (data.brands.length === 0) {

            container.innerHTML =
                "<p>No brands have been added yet.</p>";

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
                            Brand Name
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

        `;


        data.brands.forEach(
            function (brand) {

                html += `

                    <tr>

                        <td>
                            ${brand.brand_id}
                        </td>

                        <td>
                            ${escapeHtml(
                                brand.brand_name
                            )}
                        </td>

                        <td>

                            <a
                                class="edit-btn"
                                href="${
                                    window.APP_BASE
                                }/views/admin/brand.php?edit_id=${
                                    brand.brand_id
                                }">

                                Edit

                            </a>


                            <button
                                type="button"
                                class="delete-btn"
                                data-brand-id="${
                                    brand.brand_id
                                }">

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


        container.innerHTML = html;

    })

    .catch(error => {

        console.error(error);

        document.getElementById(
            "brandTableContainer"
        ).innerHTML =
            `<div class="server-error">
                Unable to load brands.
             </div>`;

    });

}


function loadBrandForEditing(id)
{

    fetch(
        window.APP_BASE +
        "/api/brand_api.php?id=" +
        encodeURIComponent(id)
    )

    .then(response => response.text())

    .then(text => {

        const data =
            parseJsonResponse(text);


        if (!data.success) {

            document.getElementById(
                "brandMessage"
            ).innerHTML =
                `<div class="server-error">
                    ${escapeHtml(data.error || "Unable to load the brand.")}
                 </div>`;

            return;
        }


        document.getElementById(
            "brand_name"
        ).value =
            data.brand.brand_name;

    })

    .catch(error => {

        console.error(error);

    });

}



function deleteBrand(id)
{
    const confirmed =
        confirm("Are you sure you want to delete this brand?");

    if (!confirmed) {
        return;
    }

    const formData =
        new FormData();

    formData.append(
        "brand_id",
        id
    );

    fetch(
        window.APP_BASE +
        "/actions/delete_brand_action.php",
        {
            method: "POST",
            body: formData
        }
    )

    .then(response => {

        return response.text();

    })

    .then(text => {

        let data;

        try {

            data =
                parseJsonResponse(text);

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
                "The server returned an unexpected response. Check the browser console."
            );

            return;

        }


        if (data.success) {

            alert(
                data.message ||
                "Brand deleted successfully."
            );

            loadBrands();

        } else {

            alert(
                data.error ||
                "Unable to delete the brand."
            );

        }

    })

    .catch(error => {

        console.error(
            "FETCH ERROR:",
            error
        );

        alert(
            "Something went wrong while deleting the brand."
        );

    });
}


function escapeHtml(value)
{

    const div =
        document.createElement("div");


    div.textContent =
        value;


    return div.innerHTML;

}

</script>


<?php

require_once __DIR__ . '/../layout/footer.php';

?>
