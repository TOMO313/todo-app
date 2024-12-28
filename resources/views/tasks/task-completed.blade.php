<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Completed Task') }}
        </h2>
    </x-slot>

    <div class="flex justify-center">
        <div id="chart_div" class="mt-10"></div>
    </div>

    <script src="https://www.gstatic.com/charts/loader.js"></script>
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
</x-app-layout>