<template>
    <DefaultField
        :field="currentField"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <div class="flex items-center">
                <a
                    :href="currentField.zoomMeeting.start_url"
                    class="link-default mr-4"
                    target="_blank"
                >
                    Start
                </a>

                <copy-button @click="copy(currentField.zoomMeeting.start_url)"></copy-button>
            </div>

            <div class="flex items-center mb-2">
                <a
                    :href="currentField.zoomMeeting.join_url"
                    class="link-default mr-4"
                    target="_blank"
                >
                    Join
                </a>

                <copy-button @click="copy(currentField.zoomMeeting.join_url)"></copy-button>
            </div>

            <default-button
                type="button"
                @click="updateZoomMeeting"
            >
                Update
            </default-button>
        </template>
    </DefaultField>
</template>


<script>
import axios from "axios";
import { DependentFormField, HandlesValidationErrors } from 'laravel-nova'

export default {
  mixins: [DependentFormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field'],

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value || ''
    },

    /*
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      formData.append(this.field.attribute, this.value || '')
    },

    /*
     * Copy the passed value to the clipboard.
     */
    copy(copyText) {
        navigator.clipboard.writeText(copyText)
    },

    /*
     * Update zoom meeting.
     */
    async updateZoomMeeting() {
        const updatedZoomMeeting = await axios.get(`/api/meetings/${this.field.zoomMeeting.id}/update`)

        if (updatedZoomMeeting.statusText === 'OK') {
            alert('Zoom meeting has been updated')
        }
    },
  },
}
</script>
