<x-app-layout>
    <div class="tabs">
        <ul class="tab-list">
            {{-- tab-indexを指定すると、tabキーを押下して選択できる(値が大きいほど優先して選択される) --}}
            <li class="tab-item active" tabindex="0">Task List</li>
            <li class="tab-item" tabindex="0">Completed Task</li>
        </ul>
        <div class="tab-panel active">
            <div id="calendar"></div>
            <div id="modal" class="modal-outside">
                <div class="modal-inside">
                    <form method="POST" action="{{ route('task.store') }}">
                        @csrf
                        <label>タスク：</label>
                        <input id="new_task_title" class="task-title" name="task_title" type="text" value="{{ old('task_title') }}" />
                        @error('task_title')
                        <div style="color: red;">{{ $message }}</div>
                        @enderror
                        <label>説明：</label>
                        <textarea id="new_task_description" class="task-description" name="task_description">{{ old('task_description') }}</textarea>
                        <label>開始日時：</label>
                        <input id="new_start_date" class="task-date" name="start_date" type="datetime-local" value="{{ old('start_date') }}" />
                        @error('start_date')
                        <div style="color: red;">{{ $message }}</div>
                        @enderror
                        <label>終了日時：</label>
                        <input id="new_end_date" class="task-date" name="end_date" type="datetime-local" value="{{ old('end_date') }}" />
                        @error('end_date')
                        <div style="color: red;">{{ $message }}</div>
                        @enderror
                        <label>色：</label>
                        <select class=" task-color" name="task_color">
                            <option value="red" selected>赤</option>
                            <option value="blue">青</option>
                            <option value="green">緑</option>
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
                        @error('task_title')
                        <div style="color: red;">{{ $message }}</div>
                        @enderror
                        <label>説明：</label>
                        <textarea id="task_description" class="task-description" name="task_description"></textarea>
                        <label>開始日時：</label>
                        <input id="start_date" class="task-date" name="start_date" type="datetime-local" value="" />
                        @error('start_date')
                        <div style="color: red;">{{ $message }}</div>
                        @enderror
                        <label>終了日時：</label>
                        <input id="end_date" class="task-date" name="end_date" type="datetime-local" value="" />
                        @error('end_date')
                        <div style="color: red;">{{ $message }}</div>
                        @enderror
                        <label>色：</label>
                        <select id="task_color" class="task-color" name="task_color">
                            <option value="red">赤</option>
                            <option value="blue">青</option>
                            <option value="green">緑</option>
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
        </div>
        <div class="tab-panel">
            <div class="flex justify-center">
                <div id="chart_div" class="mt-10">
            </div>
        </div>
        <script>
            google.charts.load('current', {
                'packages': ['corechart'],
                'language': 'ja'
            });
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Complete');
                data.addColumn('number', 'Count');
                {{-- @jsonはbladeコンポーネントに渡した場合にコンパイルされないため、汎用的な@jsを使用。{completedCount: 1, notCompletedCount: 2}のようにPHP配列をJSON形式に変換できる。 --}}
                const count = @js($data);
                data.addRows([
                    ['Completed Task', count.completedCount],
                    ['Not Completed Task', count.notCompletedCount],
                ]);

                var options = {
                    'title': 'Your Completed Task Rate',
                    'width': 900,
                    'height': 600,
                    is3D: true,
                };

                var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        </script>
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
            z-index: 2;
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