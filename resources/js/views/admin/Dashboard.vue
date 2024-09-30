<template>
    <div>
        <h3 class="font-bold text-xl mb-4">Dashboard</h3>

        <div class="flex flex-row justify-between items-center p-4 border rounded-lg mb-4">
            <div class="flex flex-col gap-2">
                <p>Статистика за</p>
                <div class="join">
                    <button class="btn join-item" :class="[isPeriodActive('day') ? 'btn-success' : 'btn-outline']"
                            @click="setPeriod('day')">Доба
                    </button>
                    <button class="btn  join-item" :class="[isPeriodActive('week') ? 'btn-success' : 'btn-outline']"
                            @click="setPeriod('week')">Тиждень
                    </button>
                    <button class="btn join-item" :class="[isPeriodActive('month') ? 'btn-success' : 'btn-outline']"
                            @click="setPeriod('month')">Місяць
                    </button>
                    <button class="btn join-item" :class="[isPeriodActive('year') ? 'btn-success' : 'btn-outline']"
                            @click="setPeriod('year')">Рік
                    </button>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <p>Кастомний період</p>
                <div class="join">
                    <div>
                        <div>
                            <input class="input input-bordered join-item" placeholder="Start date" type="date"
                                   v-model="form.start_date"/>
                        </div>
                    </div>
                    <div>
                        <input class="input input-bordered join-item" placeholder="End date" type="date"
                               v-model="form.end_date"/>
                    </div>
                    <div class="indicator">
                        <button @click="searchStats" class="btn btn-outline join-item">Шукати</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row justify-between items-start gap-6">
            <div class="border rounded-2xl p-4 w-full">
                <p>Кількість унікальних візитів</p>
                <Bar
                    v-if="isVisitsLoaded"
                    :options="chartOptions"
                    :data="uniqueVisitsChartData"
                />
            </div>
            <div class="border rounded-2xl p-4 w-full">
                <p>Кількість кліків</p>
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
import {Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement} from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels'; // Import the plugin

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement, ChartDataLabels); // Register the plugin

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
            form: {
                start_date: null,
                end_date: null
            },
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
                plugins: {
                    datalabels: {
                        color: '#fff', // Label color (white)
                        anchor: 'center', // Anchor position
                        align: 'center', // Alignment inside the bar
                        formatter: (value, context) => {
                            return value; // Show the actual data value
                        },
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    },
    created() {
        this.setPeriod('day');
    },
    methods: {
        setPeriod(period) {
            let now = new Date();
            let start_date = new Date();
            let end_date = new Date();

            switch (period) {
                case 'day':
                    start_date.setDate(now.getDate() - 1);
                    break;
                case 'week':
                    start_date.setDate(now.getDate() - 7);
                    break;
                case 'month':
                    start_date.setMonth(now.getMonth() - 1);
                    break;
                case 'year':
                    start_date.setFullYear(now.getFullYear() - 1);
                    break;
            }

            this.form.start_date = start_date.toISOString().split('T')[0];
            this.form.end_date = end_date.toISOString().split('T')[0];

            this.searchStats();
        },
        searchStats() {
            this.fetchUniqueVisits();
            this.fetchNonUniqueVisits();
            this.fetchDeviceData();
            this.fetchBrowserData();
            this.fetchPlatformsData();
        },
        isPeriodActive(period) {
            if (period === 'day') {
                // start date is yesterday and end is today
                let start_date = new Date();
                start_date.setDate(start_date.getDate() - 1);
                return this.form.start_date === start_date.toISOString().split('T')[0]
                    && this.form.end_date === new Date().toISOString().split('T')[0]
            }

            if (period === 'week') {
                let start_date = new Date();
                start_date.setDate(start_date.getDate() - 7);
                return this.form.start_date === start_date.toISOString().split('T')[0]
            }

            if (period === 'month') {
                let start_date = new Date();
                start_date.setMonth(start_date.getMonth() - 1);
                return this.form.start_date === start_date.toISOString().split('T')[0]
            }

            if (period === 'year') {
                let start_date = new Date();
                start_date.setFullYear(start_date.getFullYear() - 1);
                return this.form.start_date === start_date.toISOString().split('T')[0]
            }
        },
        fetchDeviceData() {
            this.isDeviceDataLoaded = false;
            axios.get('/api/stats/devices', {
                params: this.form
            })
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
            this.isBrowserDataLoaded = false;
            axios.get('/api/stats/browsers', {
                params: this.form
            })
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
            this.isPlatformDataLoaded = false;
            axios.get('/api/stats/platforms', {
                params: this.form
            })
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
            this.isVisitsLoaded = false;
            axios.get('/api/stats/unique-visits', {
                params: this.form
            })
                .then(response => {

                    let labels = [];
                    let data = [];

                    response.data.data.forEach(item => {
                        let date = new Date(item.date);
                        let month = date.getMonth() + 1;
                        let dateWithoutYear = date.getDate() + '/' + (month < 10 ? '0' + month : month);
                        labels.push(dateWithoutYear);
                        data.push(item.count);
                    });

                    this.uniqueVisitsChartData.datasets[0] = {
                        label: 'Унікальні візити',
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
        fetchNonUniqueVisits() {
            this.isHourlyVisitsLoaded = false
            axios.get('/api/stats/non-unique-visits', {
                params: this.form
            })
                .then(response => {

                    let labels = [];
                    let data = [];

                    response.data.data.forEach(item => {

                        // if label is not int
                        if (isNaN(item.label)) {
                            let date = new Date(item.label);
                            let month = date.getMonth() + 1;
                            let dateWithoutYear = date.getDate() + '/' + (month < 10 ? '0' + month : month);
                            labels.push(dateWithoutYear);
                        } else {
                            labels.push(item.label);
                        }
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
