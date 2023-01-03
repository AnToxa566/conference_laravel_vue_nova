<template>
    <div>
        <span
            class="me-1 text-decoration-underline"
            style="cursor: pointer;"
            @click="clickExport"
        >
            <slot name="title"></slot>
        </span>

        <v-progress-circular
            v-if="exportLoading"

            size="15"
            width="3"

            indeterminate
        ></v-progress-circular>
    </div>
</template>


<script>
export default {
    name: 'export-button',

    emits: [
        'startExport',
    ],

    data: () => ({
        exportLoading: false,
    }),

    methods: {
        clickExport() {
            if (!this.exportLoading) {
                this.exportLoading = true
                this.$emit('startExport')
            }
        },
    }
}
</script>
