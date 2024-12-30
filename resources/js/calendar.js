//https://fullcalendar.io/docs/initialize-es6
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";
import axios from "axios";

//モーダルの外側をクリックするとモーダルが閉じる
const outsideModals = document.querySelectorAll(".modal-outside");

//querySelectorAll()を使う場合、forEach()で回す
outsideModals.forEach((outsideModal) => {
    //addEventListener()はeventオブジェクトを返し、eventオブジェクトの中にはtargetというクリックしたHTML要素が入っている
    outsideModal.addEventListener("click", function (event) {
        //closest(".modal-inside")はevent.targetの親要素にmodal-insideクラスがあればその親要素を返し、なければnullを返す。つまり、modal-insideクラス内の要素をクリックした場合は何も起こらないが、クラス外の要素をクリックした場合はモーダルが閉じる。
        if (!event.target.closest(".modal-inside")) {
            outsideModal.style.display = "none";
        }
    });
});

function formatDate(date, message, startDate) {
    //dateを元にJavaScriptのDateオブジェクト(時間要素(年、月、日、時、分、秒)を個別に取得できる)を生成
    const dt = new Date(date);
    if (startDate) {
        const startDt = new Date(startDate);
        if (message === "end_date") {
            //例えば、setDate(25)とすると、25日に設定
            dt.setDate(dt.getDate() - 1);
            if (dt < startDt) {
                dt.setDate(dt.getDate() + 1);
            }
        }
    }
    const year = dt.getFullYear();
    //先頭に0を付け、1桁の場合は1月→01、2桁の場合は12月→012。dt.getMonth()は、0～11（1月が0）なので、+1することで1～12にする。slice(-2)は、右端2個を取得する。
    const month = ("0" + (dt.getMonth() + 1)).slice(-2);
    const day = ("0" + dt.getDate()).slice(-2);
    const hours = ("0" + dt.getHours()).slice(-2);
    const minutes = ("0" + dt.getMinutes()).slice(-2);
    const seconds = ("0" + dt.getSeconds()).slice(-2);

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

let calendarEl = document.getElementById("calendar");

let calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    //https://fullcalendar.io/docs/customButtons
    customButtons: {
        taskAddButton: {
            text: "タスク追加",
            click: function () {
                document.getElementById("modal").style.display = "flex";
            },
        },
    },
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "taskAddButton dayGridMonth,timeGridWeek,listWeek",
    },
    height: 650,
    //events:は何らかの処理が行われる度に実行される。https://fullcalendar.io/docs/events-function infoオブジェクト内の構造
    events: function (info, successCallback, failureCallback) {
        axios
            .post("/task/get", {
                //valueOf()は、1970年1月1日0時0分0秒(UTC)から経過した時間をミリ秒単位で返す(1970年1月1日0時0分1秒の場合、1000ミリ秒=1秒)
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then(function (response) {
                //カレンダーに表示されているタスクを全て削除
                calendar.removeAllEvents();
                //https://fullcalendar.io/docs/event-parsing、Fullcalendarが指定するプロパティに値が入っている場合、カレンダーに表示する(task_title as titleのように、titleプロパティに値が入っている場合)
                successCallback(response.data);
            })
            .catch(function (error) {
                alert("タスクの表示に失敗しました");
            });
    },
    //https://fullcalendar.io/docs/eventClick infoオブジェクト内の構造
    eventClick: function (info) {
        //infoオブジェクトに沿った取り出し方ではないところがある
        document.getElementById("task_id").value = info.event.id;
        document.getElementById("task_title").value = info.event.title;
        document.getElementById("task_description").value =
            info.event.extendedProps.description;
        //blade内に同じid属性を指定しないように
        document.getElementById("start_date").value = formatDate(
            info.event.start
        );
        document.getElementById("end_date").value = formatDate(info.event.end);
        document.getElementById("task_color").value =
            info.event.backgroundColor;
        document.getElementById("delete-task-id").value = info.event.id;

        document.getElementById("update-modal").style.display = "flex";
    },
    selectable: true,
    //https://fullcalendar.io/docs/select-callback infoオブジェクト内の構造
    select: function (info) {
        document.getElementById("new_start_date").value = formatDate(
            info.start
        );
        document.getElementById("new_end_date").value = formatDate(
            info.end,
            "end_date",
            info.start
        );

        document.getElementById("modal").style.display = "flex";
    },
});

calendar.render();

//bladeからonclickでメソッドを発火させたい時はjsでwindow.メソッド名 = function(){}としなければいけない。または、document.getElementById('id属性値').addEventListener('click', function(){})と記述.
window.closeModal = function () {
    document.getElementById("modal").style.display = "none";
};

window.closeUpdateModal = function () {
    document.getElementById("update-modal").style.display = "none";
};

window.deleteTask = function () {
    "use strict";
    if (confirm("本当に削除しますか？")) {
        document.getElementById("delete-task-form").submit();
    }
};
