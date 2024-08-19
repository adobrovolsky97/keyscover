<template>
    <div>
        <h3 class="font-bold text-xl mb-4">Dashboard</h3>

        <div class="flex flex-col lg:flex-row justify-between items-start gap-6">
            <div class="border rounded-2xl p-4 w-full">
                <p>Кількість унікальних користувачів за минулий тиждень</p>
                <Bar
                    v-if="isVisitsLoaded"
                    id="my-chart-id"
                    :options="chartOptions"
                    :data="uniqueVisitsChartData"
                />
            </div>
            <div class="border rounded-2xl p-4 w-full">
                <p>Погодинна кількість не унікальних візитів за сьогодні</p>
                <Bar
                    v-if="isHourlyVisitsLoaded"
                    id="my-chart-id"
                    :options="chartOptions"
                    :data="hourlyVisitsChartData"
                />
            </div>
        </div>
    </div>
</template>
<script>
import {Bar} from 'vue-chartjs'
import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
    name: "Dashboard",
    components: {Bar},
    created() {
        this.fetchUniqueVisits();
        this.fetchHourlyVisits();
    },
    data() {
        return {
            isVisitsLoaded: false,
            isHourlyVisitsLoaded: false,
            uniqueVisitsChartData: {
                labels: [],
                datasets: []
            },
            hourlyVisitsChartData: {
                labels: [],
                datasets: []
            },
            chartOptions: {
                responsive: true
            }
        }
    },
    methods: {
        fetchUniqueVisits() {
            axios.get('/api/stats/unique-visits')
                .then(response => {

                    let labels = [];
                    let data = [];

                    response.data.data.forEach(item => {
                        labels.push(item.date);
                        data.push(item.count);
                    });

                    this.uniqueVisitsChartData.datasets[0] = {
                        label: 'Візити за тиждень',
                        backgroundColor: '#f87979',
                        data: data
                    };

                    this.uniqueVisitsChartData.labels = labels;

                    // this.chartData = response.data.data
                    this.isVisitsLoaded = true
                })
                .catch(error => {
                    console.log(error)
                })
        },
        fetchHourlyVisits() {
            axios.get('/api/stats/visits-by-hour')
                .then(response => {

                    let labels = [];
                    let data = [];

                    response.data.data.forEach(item => {

                        if (item.hour < 10) item.hour = '0' + item.hour;

                        labels.push(item.hour);
                        data.push(item.count);
                    });

                    this.hourlyVisitsChartData.datasets[0] = {
                        label: 'Погодинні візити',
                        backgroundColor: '#79b4f8',
                        data: data
                    };

                    this.hourlyVisitsChartData.labels = labels;

                    // this.chartData = response.data.data
                    this.isHourlyVisitsLoaded = true
                })
                .catch(error => {
                    console.log(error)
                })
        }
    }
}
</script>
