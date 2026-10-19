document.addEventListener("DOMContentLoaded", function () {

    document.addEventListener("click", function (event) {

        const link = event.target.closest("a[href*='add_to_cart_action.php']");

        if (!link) {
            return;
        }

        event.preventDefault();

        if (link.classList.contains("is-loading")) {
            return;
        }

        link.classList.add("is-loading");

        window.ajaxRequest(link.href)

            .then(function (data) {

                window.showToast(data.message, data.success ? "success" : "error");

                if (data.cart_count !== undefined) {
                    window.updateCartBar(data.cart_count, data.cart_total);
                }

                if (data.success) {
                    link.classList.add("added");
                    setTimeout(function () { link.classList.remove("added"); }, 1400);
                }
            })

            .catch(function () {
                window.location.href = link.href;
            })

            .finally(function () {
                link.classList.remove("is-loading");
            });
    });


    const table = document.querySelector(".cart-table");

    if (!table) {
        return;
    }

    const section = document.querySelector(".cart-section");


    function showEmptyCart() {

        ["#removeForm", "#updateForm", ".cart-table", ".cart-actions"].forEach(function (selector) {
            const el = section.querySelector(selector);
            if (el) {
                el.remove();
            }
        });

        const empty = document.createElement("div");
        empty.className = "empty-state";

        const text = document.createElement("p");
        text.textContent = "Your cart is empty.";

        const back = document.createElement("a");
        back.className = "btn cart-link-btn";
        back.href = window.APP_BASE_URL + "/index.php";
        back.textContent = "Continue Shopping";

        empty.appendChild(text);
        empty.appendChild(back);
        section.appendChild(empty);
    }


    function applySummary(data) {

        if (data.cart_count !== undefined) {
            window.updateCartBar(data.cart_count, data.cart_total);
        }

        const subtotal = document.getElementById("cartSubtotal");

        if (subtotal && data.cart_total !== undefined) {
            subtotal.textContent = window.formatMoney(data.cart_total);
            subtotal.classList.remove("flash-update");
            void subtotal.offsetWidth;
            subtotal.classList.add("flash-update");
        }
    }


    function handleRemoved(data) {

        const ids = data.removed || [];

        ids.forEach(function (id) {

            const row = table.querySelector("tr[data-product-id='" + id + "']");

            if (!row) {
                return;
            }

            row.classList.add("row-removing");

            setTimeout(function () {
                row.remove();

                if (!table.querySelector("tbody tr")) {
                    showEmptyCart();
                }
            }, 380);
        });
    }


    function handleUpdated(data) {

        (data.lines || []).forEach(function (line) {

            const row = table.querySelector("tr[data-product-id='" + line.id + "']");

            if (!row) {
                return;
            }

            const qtyInput = row.querySelector(".qty-input");
            const lineTotal = row.querySelector(".line-total");

            if (qtyInput) {
                qtyInput.value = line.qty;
            }

            if (lineTotal) {
                lineTotal.textContent = window.formatMoney(line.line_total);
                lineTotal.classList.remove("flash-update");
                void lineTotal.offsetWidth;
                lineTotal.classList.add("flash-update");
            }
        });
    }


    document.addEventListener("submit", function (event) {

        const form = event.target;

        const isRemove = form.id === "removeForm" || form.classList.contains("inline-form");
        const isUpdate = form.id === "updateForm";

        if (!isRemove && !isUpdate) {
            return;
        }

        if (event.defaultPrevented) {
            return;
        }

        event.preventDefault();

        const body = new FormData(form);

        if (isRemove && body.getAll("remove[]").length === 0) {
            window.showToast("Select at least one item to remove.", "error");
            return;
        }

        const submitter = event.submitter;

        if (submitter) {
            submitter.classList.add("is-loading");
            submitter.disabled = true;
        }

        window.ajaxRequest(form.action, { method: "POST", body: body })

            .then(function (data) {

                window.showToast(data.message, data.success ? "success" : "error");

                if (!data.success) {
                    return;
                }

                applySummary(data);

                if (isRemove) {
                    handleRemoved(data);
                } else {
                    handleUpdated(data);
                }
            })

            .catch(function () {
                form.submit();
            })

            .finally(function () {
                if (submitter) {
                    submitter.classList.remove("is-loading");
                    submitter.disabled = false;
                }
            });
    });

});
