//https://fullcalendar.io/docs/initialize-es6
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import axios from "axios";

function formatDate(date) {
    //dateを元にJavaScriptのDateオブジェクト(時間要素(年、月、日、時、分、秒)を個別に取得できる)を生成
    const dt = new Date(date);
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
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
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
    height: 730,
    //events:は何らかの処理が行われる度に実行される。infoはstartとendオブジェクトがあり、start: Sun Dec 01 2024, end: Sun Jan 12 2025のように、表示されているカレンダーの開始と終了日付が格納されている
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
                console.log(response.data);
                //https://fullcalendar.io/docs/event-parsing、Fullcalendarが指定するプロパティに値が入っている場合、カレンダーに表示する(task_title as titleのように、titleプロパティに値が入っている場合)
                successCallback(response.data);
            })
            .catch(function (error) {
                alert("タスクの表示に失敗しました");
            });
    },
    eventClick: function (info) {
        //infoオブジェクトに沿った取り出し方ではないところがある
        document.getElementById("task_id").value = info.event.id;
        document.getElementById("task_title").value = info.event.title;
        document.getElementById("task_description").value =
            info.event.extendedProps.description;
        document.getElementById("start_date").value = formatDate(
            info.event.start
        );
        document.getElementById("end_date").value = formatDate(info.event.end);
        document.getElementById("task_color").value =
            info.event.backgroundColor;
        document.getElementById("delete-task-id").value = info.event.id;

        document.getElementById("update-modal").style.display = "flex";
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
