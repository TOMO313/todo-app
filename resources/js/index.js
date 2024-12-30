//document.querySelectorAll()は配列として返すのではなく、NodeListとして返す。NodeListはforEachを利用できるが、indexOf()などの配列に使用できるメソッドは使用できない。
const tabItems = document.querySelectorAll(".tab-item");

tabItems.forEach((tabItem) => {
    //tabItemsに含まれるtabItemそれぞれにclickイベントを設置し、clickされた場合、それ以降「tabItem = クリックされたtabItem」となる
    tabItem.addEventListener("click", () => {
        //一旦、全てのタブをクリックされていない表示にするため、tabItemではなく、tとして新しく作成
        tabItems.forEach((t) => {
            t.classList.remove("active");
        });
        const tabPanels = document.querySelectorAll(".tab-panel");
        tabPanels.forEach((tabPanel) => {
            tabPanel.classList.remove("active");
        });
        tabItem.classList.add("active");
        //tabItemsはNodeListなので、Array.from()で配列として返す。indexOf()でクリックされたtabItemのインデックスを算出。
        const tabIndex = Array.from(tabItems).indexOf(tabItem);
        tabPanels[tabIndex].classList.add("active");
    });
});
