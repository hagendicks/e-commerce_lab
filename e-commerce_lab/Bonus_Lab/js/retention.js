document.addEventListener("DOMContentLoaded", function () {

    function updateWishCount(count) {

        const badge = document.getElementById("wishCount");

        if (badge && typeof count === "number") {
            badge.textContent = count;
        }
    }


    function setHearts(productId, active) {

        document
            .querySelectorAll('[data-product-id="' + productId + '"]')
            .forEach(function (el) {

                if (el.classList.contains("wish-btn") || el.classList.contains("wish-link")) {
                    el.classList.toggle("active", active);
                }
            });
    }


    function removeCard(link) {

        const card = link.closest(".product-card");

        if (!card) {
            return;
        }

        card.classList.add("wish-removed");

        setTimeout(function () {

            card.remove();

            if (!document.querySelector("[data-wishlist-page] .product-card")) {
                window.location.reload();
            }

        }, 300);
    }


    document.addEventListener("click", function (event) {

        const link = event.target.closest("a.wish-btn, a.wish-link");

        if (!link) {
            return;
        }

        event.preventDefault();

        if (link.dataset.busy) {
            return;
        }

        link.dataset.busy = "1";

        window.ajaxRequest(link.href)
            .then(function (data) {

                if (data.login_required) {

                    window.showToast(data.message, "error");

                    setTimeout(function () {
                        window.location.href = data.redirect;
                    }, 900);

                    return;
                }

                if (!data.success) {
                    window.showToast(data.message || "Something went wrong.", "error");
                    return;
                }

                setHearts(link.dataset.productId, !!data.added);

                updateWishCount(data.wish_count);

                window.showToast(data.message, "success");

                if (!data.added && document.querySelector("[data-wishlist-page]")) {
                    removeCard(link);
                }
            })
            .catch(function () {
                window.showToast("Network error. Please try again.", "error");
            })
            .finally(function () {
                delete link.dataset.busy;
            });
    });


    const banner = document.getElementById("cartReminder");

    if (banner) {

        let dismissed = false;

        try {
            dismissed = sessionStorage.getItem("cartReminderDismissed") === "1";
        } catch (error) {
            dismissed = false;
        }

        banner.hidden = dismissed;

        const close = document.getElementById("cartReminderClose");

        if (close) {

            close.addEventListener("click", function () {

                banner.hidden = true;

                try {
                    sessionStorage.setItem("cartReminderDismissed", "1");
                } catch (error) {
                }
            });
        }
    }

});
