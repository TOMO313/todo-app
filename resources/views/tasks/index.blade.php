<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Index') }}
        </h2>
    </x-slot>

    <div id="calendar"></div>

    <div id="modal" class="modal-outside">
        <div class="modal-inside">
            <form method="POST" action="{{ route('task.store') }}">
                @csrf
                <label>タスク：</label>
                <input class="task-title" name="task_title" type="text" />
                <label>説明：</label>
                <textarea class="task-description" name="task_description"></textarea>
                <label>開始日時：</label>
                <input class="task-date" name="start_date" type="datetime-local" />
                <label>終了日時：</label>
                <input class="task-date" name="end_date" type="datetime-local" />
                <label>色：</label>
                <select class="task-color" name="task_color">
                    <option value="red" selected>赤</option>
                    <option value="blue">青</option>
                    <option value="yellow">黄</option>
                </select>
                <div class="modal-button">
                    {{-- type="button"がないとonclickが発火しない --}}
                    <button type="button" onclick="closeModal()">キャンセル</button>
                    <button type="submit">追加</button>
                </div>
            </form>
        </div>
    </div>

    <div id="update-modal" class="modal-outside">
        <div class="modal-inside">
            <form method="POST" action="{{ route('task.update') }}">
                @csrf
                @method('PUT')
                <input id="task_id" name="task_id" type="hidden" value="" />
                <label>タスク：</label>
                <input id="task_title" class="task-title" name="task_title" type="text" value="" />
                <label>説明：</label>
                <textarea id="task_description" class="task-description" name="task_description"></textarea>
                <label>開始日時：</label>
                <input id="start_date" class="task-date" name="start_date" type="datetime-local" value="" />
                <label>終了日時：</label>
                <input id="end_date" class="task-date" name="end_date" type="datetime-local" value="" />
                <label>色：</label>
                <select id="task_color" class="task-color" name="task_color">
                    <option value="red">赤</option>
                    <option value="blue">青</option>
                    <option value="yellow">黄</option>
                </select>
                <label><input id="is_completed" type="radio" name="is_completed" value="true" />完了</label>
                <label><input id="is_completed" type="radio" name="is_completed" value="false" checked />未完了</label>
                <div class="modal-button">
                    <button type="button" onclick="closeUpdateModal()">キャンセル</button>
                    <button type="submit">更新</button>
                </div>
            </form>
            <form id="delete-task-form" class="delete-task-form" method="POST" action="{{ route('task.delete') }}">
                @csrf
                @method('DELETE')
                <input id="delete-task-id" name="delete_task_id" type="hidden" value="" />
                <button class="delete-button" type="button" onclick="deleteTask()">削除</button>
            </form>
        </div>
    </div>

    <style>
        .modal-outside {
            /*要素を消す*/
            display: none;
            /*要素がdisplay:flexの時に水平方向でセンタリング*/
            justify-content: center;
            /*要素がdisplay:flexの時に垂直方向でセンタリング*/
            align-items: center;
            /*ページ全体に対する配置*/
            position: absolute;
            /*要素が重なる時に大きい値ほど上に重なる*/
            z-index: 1;
            /*top、left、right、bottomを0にすることで画面全体を覆う形にする*/
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /*赤、緑、青は0で黒。透明度は50%で半透明な黒。*/
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-inside {
            background-color: white;
            height: 70%;
            width: 80%;
            /*要素内の配置: 上下左右*/
            padding: 5%;
        }

        .task-title {
            /*HTMLの<h1></h1>のように行全体をブロック化し、前後の要素を同じ行に表示させない*/
            display: block;
            width: 80%;
            margin: 0 0 10px;
            /*枠線: 一本線 黒*/
            border: solid black;
            /*枠線の丸み*/
            border-radius: 5px;
        }

        .task-date {
            width: 40%;
            /*前後の要素との間隔: 上 右 下 左*/
            margin: 10px 5px 20px 5px;
            border: solid black;
            border-radius: 5px;
        }

        textarea {
            display: block;
            width: 80%;
            margin: 0 0 10px;
            border: solid black;
            border-radius: 5px;
            resize: none;
        }

        select {
            display: block;
            width: 20%;
            margin: 0 0 10px;
            border: solid black;
            border-radius: 5px;
        }

        .modal-button {
            /*子要素(2つのbuttonタグ)をflexで横並びにして、flexと相性が良いjustify-content: centerで水平方向にセンタリングさせる*/
            display: flex;
            justify-content: center;
            gap: 50px;
        }

        button[type="button"] {
            background-color: blue;
            border: solid blue;
            border-radius: 5px;
        }

        /*hoverで色変化*/
        button[type="button"]:hover {
            background-color: yellow;
        }

        button[type="submit"] {
            background-color: blue;
            border: solid blue;
            border-radius: 5px;
        }

        button[type="submit"]:hover {
            background-color: yellow;
        }

        /*.fc-event-title-containerはFullcalendarに表示されているタスクの要素を指定*/
        .fc-event-title-container {
            cursor: pointer;
        }

        .delete-task-form {
            /*text-alignは水平方向の揃え方を指定するプロパティ*/
            text-align: center;
        }

        .delete-button {
            margin: 40px 0 0 0;
        }
    </style>
</x-app-layout>