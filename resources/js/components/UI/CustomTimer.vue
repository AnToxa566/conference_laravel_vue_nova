<template>
    <span v-if="this.visible">{{ this.timestampToHumanDate }}</span>
</template>


<script>
export default {
    name: 'custom-timer',

    emits: [
        'update:modelValue',
        'timerStoped',
    ],

    data: () => ({
        leftTime: {
            timestamp: null,
            seconds: null,
            minutes: null,
            hours: null,
            days: null,
            months: null,
            years: null,
        },
        timer: null,
    }),

    props: {
        modelValue: [Object],

        toTimestamp: {
            type: Number,
            required: true,
        },

        visible: {
            type: Boolean,
            required: false,
            default: true,
        },
    },

    mounted() {
        this.leftTime.timestamp = Math.round((this.toTimestamp - Date.now()) / 1000)

        this.startTimer()
    },

    destroyed() {
        this.stopTimer()
    },

    methods: {
        startTimer() {
            this.timer = setInterval(() => {
                this.leftTime.timestamp--

                this.leftTime.seconds = this.leftTime.timestamp % 60
                this.leftTime.minutes = Math.floor((this.leftTime.timestamp / 60) % 60)
                this.leftTime.hours = Math.floor((this.leftTime.timestamp / (60 * 60)) % 24)
                this.leftTime.days = Math.floor((this.leftTime.timestamp / (60 * 60 * 24)) % 24)
                this.leftTime.months = Math.floor((this.leftTime.timestamp / (60 * 60 * 24 * 30)) % 30)
                this.leftTime.years = Math.floor((this.leftTime.timestamp / (60 * 60 * 24 * 30 * 365)) % 365)
            }, 1000)
        },

        stopTimer() {
            clearTimeout(this.timer)
        },
    },

    computed: {
        timestampToHumanDate() {
            if (this.leftTime.years) {
                return this.leftTime.years + ' year' + (this.leftTime.years > 1 ? 's' : '')
            }

            if (this.leftTime.months) {
                return this.leftTime.months + ' month' + (this.leftTime.months > 1 ? 's' : '')
            }

            if (this.leftTime.days) {
                return this.leftTime.days + ' day' + (this.leftTime.days > 1 ? 's' : '')
            }

            if (this.leftTime.hours) {
                return this.leftTime.hours + ' hour' + (this.leftTime.hours > 1 ? 's' : '')
            }

            if (this.leftTime.minutes) {
                return this.leftTime.minutes + ' minute' + (this.leftTime.minutes > 1 ? 's' : '')
            }

            return this.leftTime.seconds + ' second' + (this.leftTime.seconds > 1 ? 's' : '')
        },
    },

    watch: {
        leftTime: {
            handler(newValue) {
                this.$emit('update:modelValue', newValue)

                if (newValue.timestamp <= 0) {
                    this.stopTimer()
                    this.$emit('timerStoped')
                }
            },

            deep: true
        }
    },
}
</script>
