<template>
    <v-expansion-panel>
        <v-expansion-panel-title>
            <slot name="title"></slot>
        </v-expansion-panel-title>

        <!-- TODO: Для min и max высчитывать какая мин и макс дата конфы -->
        <!-- TODO: v-model -->

        <v-expansion-panel-text>
            <custom-datepicker
                :hideDetails="true"
            >
            </custom-datepicker>
        </v-expansion-panel-text>
    </v-expansion-panel>
</template>


<script>
export default {

}
</script>
