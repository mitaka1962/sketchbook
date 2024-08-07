(function () {
    let controller;
    const years_arr = JSON.parse(document.getElementById("sketchbook_years_data").text);    // 昇順
    const current_year_elm = document.getElementById("sketchbook_year");
    const sketchbook_grid_elm = document.getElementById("sketchbook_grid");
    const previous_year_btn = document.getElementById("sketchbook_previous_year_btn");
    const next_year_btn = document.getElementById("sketchbook_next_year_btn");

    // 画像データがない場合
    if (years_arr.length == 0) {
        previous_year_btn.disabled = true;
        next_year_btn.disabled = true;
        return;
    }

    let current_year_idx = years_arr.length - 1;
    reload_sketches();

    // 前年ボタン
    previous_year_btn.addEventListener("click", () => {
        current_year_idx--;
        reload_sketches(); 
    });

    // 翌年ボタン
    next_year_btn.addEventListener("click", () => {
        current_year_idx++;
        reload_sketches(); 
    });

    async function reload_sketches() {
        // 既に実行中のフェッチを中止 & コントローラを新たに用意
        controller?.abort();
        controller = new AbortController();
        const signal = controller.signal;
        
        // ボタンとラベルの更新
        const current_year = years_arr[current_year_idx];
        current_year_elm.textContent = current_year;
        previous_year_btn.disabled = (current_year_idx == 0) ? true : false;
        next_year_btn.disabled = (current_year_idx == years_arr.length - 1) ? true : false;

        //　グリッドレイアウト内をクリア
        while (sketchbook_grid_elm.firstChild) {
            sketchbook_grid_elm.removeChild(sketchbook_grid_elm.firstChild);
        }

        try {
            // サーバから画像データを取得するためにGETリクエスト
            const response = await fetch(`sketchbook_server.php?year=${current_year}`, {signal});
            if (!response.ok) {
                throw new Error("response is not ok");
            }
            const image_data_arr = await response.json();

            // グリッドレイアウト内の更新
            update_grid_layout(image_data_arr);
        } catch (error) {
            if (error.name == "AbortError") {
                /* pass */
            } else {
                console.error(error);
            }            
        }
    }
    
    function update_grid_layout(image_data_arr) {
        let current_month = 0;

        for (const image_data of image_data_arr) {
            // 月ラベルを追加
            if (current_month != image_data["month"]) {
                current_month = image_data["month"];
                const newMonthDiv = document.createElement("div");
                newMonthDiv.className = "sketchbook_month";
                newMonthDiv.textContent = current_month + "月";
                sketchbook_grid_elm.appendChild(newMonthDiv);
            }

            // Luminous.js用の<a>を用意
            const newLuminousA = document.createElement("a");
            newLuminousA.className = "luminous_gallery";
            newLuminousA.href = image_data["image_src"];

            // 画像を用意
            const newContainerDiv = document.createElement("div");
            newContainerDiv.className = "sketchbook_image_container";
            const newImg = document.createElement("img");
            newImg.src = image_data["image_src"];
            newImg.alt = image_data["desc"];

            // 子要素として追加
            newContainerDiv.appendChild(newImg);
            newLuminousA.appendChild(newContainerDiv);
            sketchbook_grid_elm.appendChild(newLuminousA);
        }

        // Luminous.jsの有効化（alt属性をキャプションに設定）
        new LuminousGallery(document.querySelectorAll(".luminous_gallery"), {}, {
            caption: (trigger) => {
                if (trigger.querySelector("img").hasAttribute("alt")) {
                    return trigger.querySelector("img").alt;
                } else {
                    return "";
                }
            }
        });
    }
})();
