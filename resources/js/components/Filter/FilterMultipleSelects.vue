<template>
    <v-expansion-panel>
        <v-expansion-panel-title>
            <slot name="title"></slot>
        </v-expansion-panel-title>

        <!-- TODO: v-model -->

        <v-expansion-panel-text
            class="py-2"
        >
            <v-select
                :items="['California', 'Colorado', 'Florida', 'Georgia', 'Texas', 'Wyoming']"

                label="Select"
                variant="solo"

                multiple
                clearable
                hide-details
            ></v-select>
        </v-expansion-panel-text>
    </v-expansion-panel>
</template>


<script>
export default {

}
</script>
