(function () {

    function parseJson(text) {
        try {
            return JSON.parse(text);
        } catch (error) {
            const start = text.indexOf("{");
            const end = text.lastIndexOf("}");

            if (start === -1 || end < start) {
                throw error;
            }

            return JSON.parse(text.slice(start, end + 1));
        }
    }


    window.ajaxRequest = function (url, options) {

        const settings = Object.assign({ method: "GET" }, options || {});

        settings.headers = Object.assign(
            { "X-Requested-With": "XMLHttpRequest" },
            settings.headers || {}
        );

        return fetch(url, settings)
            .then(function (response) { return response.text(); })
            .then(parseJson);
    };


    window.showToast = function (message, type) {

        let root = document.getElementById("toastRoot");

        if (!root) {
            root = document.createElement("div");
            root.id = "toastRoot";
            root.className = "toast-root";
            document.body.appendChild(root);
        }

        const toast = document.createElement("div");
        toast.className = "toast " + (type === "error" ? "toast-error" : "toast-success");
        toast.setAttribute("role", "status");
        toast.textContent = message;

        root.appendChild(toast);

        setTimeout(function () {
            toast.classList.add("toast-leave");
            setTimeout(function () { toast.remove(); }, 350);
        }, 3200);
    };


    window.formatMoney = function (value) {
        return "$" + Number(value).toFixed(2);
    };


    window.updateCartBar = function (count, total) {

        const countEl = document.getElementById("cartCount");
        const totalEl = document.getElementById("cartTotal");

        if (!countEl || !totalEl) {
            return;
        }

        countEl.textContent = count;
        totalEl.textContent = window.formatMoney(total);

        [countEl, totalEl].forEach(function (el) {
            el.classList.remove("bump");
            void el.offsetWidth;
            el.classList.add("bump");
        });
    };


    document.addEventListener("DOMContentLoaded", function () {

        document.querySelectorAll(".flash-banner").forEach(function (banner) {
            setTimeout(function () {
                banner.classList.add("flash-hide");
                setTimeout(function () { banner.remove(); }, 450);
            }, 4500);
        });


        const form = document.querySelector(".header-search");
        const box = document.getElementById("searchSuggestions");

        if (!form || !box) {
            return;
        }

        const input = form.querySelector("input[name='user_query']");

        let timer = null;
        let latest = 0;

        function closeBox() {
            box.hidden = true;
            box.innerHTML = "";
        }

        function render(results, query) {

            box.innerHTML = "";

            if (results.length === 0) {
                const empty = document.createElement("div");
                empty.className = "suggestion-empty";
                empty.textContent = "No matches for \"" + query + "\"";
                box.appendChild(empty);
                box.hidden = false;
                return;
            }

            results.forEach(function (item, index) {

                const link = document.createElement("a");
                link.className = "suggestion";
                link.href = item.url;
                link.style.setProperty("--i", index);

                if (item.image) {
                    const img = document.createElement("img");
                    img.src = item.image;
                    img.alt = "";
                    link.appendChild(img);
                } else {
                    const blank = document.createElement("span");
                    blank.className = "suggestion-blank";
                    link.appendChild(blank);
                }

                const title = document.createElement("span");
                title.className = "suggestion-title";
                title.textContent = item.title;
                link.appendChild(title);

                const price = document.createElement("span");
                price.className = "suggestion-price";
                price.textContent = "$" + item.price;
                link.appendChild(price);

                box.appendChild(link);
            });

            box.hidden = false;
        }

        input.addEventListener("input", function () {

            clearTimeout(timer);

            const query = input.value.trim();

            if (query.length < 2) {
                closeBox();
                return;
            }

            timer = setTimeout(function () {

                const ticket = ++latest;

                window.ajaxRequest(
                    window.APP_BASE_URL + "/api/search_api.php?q=" + encodeURIComponent(query)
                )
                    .then(function (data) {
                        if (ticket !== latest) {
                            return;
                        }
                        render(data.results || [], query);
                    })
                    .catch(function () {
                        closeBox();
                    });

            }, 250);
        });

        input.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                closeBox();
            }
        });

        document.addEventListener("click", function (event) {
            if (!form.contains(event.target)) {
                closeBox();
            }
        });
    });

})();
