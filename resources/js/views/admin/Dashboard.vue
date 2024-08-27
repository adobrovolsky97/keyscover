<template>
    <div>
        <h3 class="font-bold text-xl mb-4">Dashboard</h3>

        <div class="flex flex-col lg:flex-row justify-between items-start gap-6">
            <div class="border rounded-2xl p-4 w-full">
                <p>Кількість унікальних користувачів за минулий тиждень</p>
                <Bar
                    v-if="isVisitsLoaded"
                    :options="chartOptions"
                    :data="uniqueVisitsChartData"
                />
            </div>
            <div class="border rounded-2xl p-4 w-full">
                <p>Погодинна кількість кліків за сьогодні</p>
                <Bar
                    v-if="isHourlyVisitsLoaded"
                    :options="chartOptions"
                    :data="hourlyVisitsChartData"
                />
            </div>
        </div>
        <div class="flex flex-col mt-4 lg:flex-row justify-start items-start gap-6">
            <div class="border rounded-2xl p-4 w-full">
                <p>Девайси користувачів, %</p>
                <Pie
                    v-if="isDeviceDataLoaded"
                    :options="chartOptions"
                    :data="devicesChartData"
                />
            </div>
            <div class="border rounded-2xl p-4 w-full">
                <p>Браузери користувачів, %</p>
                <Pie
                    v-if="isBrowserDataLoaded"
                    :options="chartOptions"
                    :data="browsersChartData"
                />
            </div>
            <div class="border rounded-2xl p-4 w-full">
                <p>Операційні системи користувачів, %</p>
                <Pie
                    v-if="isPlatformDataLoaded"
                    :options="chartOptions"
                    :data="platformsChartData"
                />
            </div>
        </div>
    </div>
</template>
<script>
import {Bar, Pie} from 'vue-chartjs'
import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

export default {
    name: "Dashboard",
    components: {Bar, Pie},
    data() {
        return {
            isVisitsLoaded: false,
            isHourlyVisitsLoaded: false,
            isBrowserDataLoaded: false,
            isPlatformDataLoaded: false,
            isDeviceDataLoaded: false,
            uniqueVisitsChartData: {
                labels: [],
                datasets: []
            },
            devicesChartData: {
                labels: [],
                datasets: []
            },
            browsersChartData: {
                labels: [],
                datasets: []
            },
            platformsChartData: {
                labels: [],
                datasets: []
            },
            hourlyVisitsChartData: {
                labels: [],
                datasets: []
            },
            chartOptions: {
                responsive: true,
            }
        }
    },
    created() {
        this.fetchUniqueVisits();
        this.fetchHourlyVisits();
        this.fetchDeviceData();
        this.fetchBrowserData();
        this.fetchPlatformsData();
    },
    methods: {
        fetchDeviceData() {
            axios.get('/api/stats/devices')
                .then(response => {
                    let labels = [];
                    let data = [];

                    response.data.data.forEach(item => {
                        labels.push(item.device);
                        data.push(item.percentage);
                    });

                    this.devicesChartData = {
                        labels: labels,
                        datasets: [
                            {
                                backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16', '#16DDB9FF'],
                                data: data
                            }
                        ]
                    };

                    this.isDeviceDataLoaded = true
                })
                .catch(error => {
                    console.log(error)
                })
        },
        fetchBrowserData() {
            axios.get('/api/stats/browsers')
                .then(response => {
                    let labels = [];
                    let data = [];

                    response.data.data.forEach(item => {
                        labels.push(item.browser);
                        data.push(item.percentage);
                    });

                    this.browsersChartData = {
                        labels: labels,
                        datasets: [
                            {
                                backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16', '#16DDB9FF'],
                                data: data
                            }
                        ]
                    };

                    this.isBrowserDataLoaded = true
                })
                .catch(error => {
                    console.log(error)
                })
        },
        fetchPlatformsData() {
            axios.get('/api/stats/platforms')
                .then(response => {
                    let labels = [];
                    let data = [];

                    response.data.data.forEach(item => {
                        labels.push(item.os);
                        data.push(item.percentage);
                    });

                    this.platformsChartData = {
                        labels: labels,
                        datasets: [
                            {
                                backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16', '#16DDB9FF'],
                                data: data
                            }
                        ]
                    };

                    this.isPlatformDataLoaded = true
                })
                .catch(error => {
                    console.log(error)
                })
        },
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
                        label: 'Кліки',
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
