<template>
    <v-breadcrumbs
        v-if="breadCrumbs"
        class="mx-0 my-3 px-5 bg-grey-darken-4 rounded-pill"
        :items="breadCrumbs"
    >
        <template v-slot:prepend>
            <v-icon size="small" icon="mdi-home"></v-icon>
        </template>

        <template v-slot:divider>
            <v-icon icon="mdi-chevron-right"></v-icon>
        </template>

        <template v-slot:item="{ item }">
            <v-breadcrumbs-item
                :to="item.to"
                :disabled="item.disabled"
                exact

                class="text-decoration-none"
            >
                {{ item.text }}
            </v-breadcrumbs-item>
        </template>
    </v-breadcrumbs>
</template>


<script>
import { mapGetters } from 'vuex'

export default {
    name: 'bread-crumb',

    computed: {
        ...mapGetters({
            lectureById: 'lecture/lectureById',
            conferenceById: 'conference/conferenceById',
            getPathByLeafId: 'category/getPathByLeafId',
        }),

        breadCrumbs() {
            if (this.$route.meta.breadCrumb) {
                if (this.$route.meta.breadCrumb == 'conferences') {
                    return [{ text: 'Conferences', disabled: true, to: {} }]
                }
                else if (this.$route.meta.breadCrumb == 'conference') {
                    const conference = this.conferenceById(this.$route.params.id)

                    let text = conference.category_id ? conference.title + ' (' + this.getCategoriesPath(conference.category_id) + ')' : conference.title

                    return [
                        { text: 'Conferences', disabled: false, to: { name: 'conferences' } },
                        { text: text, disabled: true, to: {} },
                    ]
                }
                else if (this.$route.meta.breadCrumb == 'lecture') {
                    const conference = this.conferenceById(this.$route.params.conference_id)
                    const lecture = this.lectureById(this.$route.params.lecture_id)

                    let conferenceText = conference.category_id ? conference.title + ' (' + this.getCategoriesPath(conference.category_id) + ')' : conference.title
                    let lectureText = lecture.category_id ? lecture.title + ' (' + this.getCategoriesPath(lecture.category_id) + ')' : lecture.title

                    return [
                        { text: 'Conferences', disabled: false, to: { name: 'conferences' } },
                        { text: conferenceText, disabled: false, to: { name: 'conferenceShow', params: { id: this.$route.params.conference_id } } },
                        { text: lectureText, disabled: true, to: {} },
                    ]
                }
            }
        },
    },


    methods: {
        getCategoriesPath(leafId) {
            let pathText = ''

            if (leafId) {
                const path = this.getPathByLeafId(leafId)

                path.forEach((val, key, arr) => {
                    if (Object.is(arr.length - 1, key)) {
                        pathText += val.title
                    }
                    else {
                        pathText += (val.title + ' > ')
                    }
                });
            }

            return pathText
        },
    },
};
</script>
