<template>
    <v-menu
        v-model="menu"
        location="bottom"
        :close-on-content-click="false"
        :open-on-click="false"
    >

        <!-- Search Text Field -->

        <template v-slot:activator="{ props }">
            <v-text-field
                v-bind="props"

                append-inner-icon="mdi-magnify"
                density="compact"
                variant="solo"

                label="Site search"
                class="mx-2 search-field"

                clearable
                single-line
                hide-details

                @update:model-value="search"
            ></v-text-field>
        </template>

        <!-- Drop Menu with Serching Result -->

        <v-card
            class="my-2"
            min-width="600"
        >
            <div class="d-flex">

                <!-- Conferences & Lectures Serching Result -->

                <v-list
                    class="px-2"
                    min-width="400"
                >

                    <!-- Conferences Serching Result -->

                    <div v-if="!radios || radios == this.radioConferences">

                        <!-- Conferences List Headert -->
                        <v-list-subheader
                            class="font-weight-bold text-white"
                        >
                            Conferences
                        </v-list-subheader>

                        <!-- Conferences not found -->
                        <div
                            v-if="this.searchedConferences.length == 0"
                            class="text-center"
                        >
                            Conferences not found
                        </div>

                        <!-- Conferences List -->
                        <v-list-item
                            v-for="conference in this.searchedConferences"

                            :key="conference.id"
                            :value="conference.id"
                            :title="conference.title"

                            prepend-icon="mdi-laptop-account"
                            @click="openConferenceInNewTab(conference.id)"
                        >
                        </v-list-item>
                    </div>

                    <!-- Horizontal Divider -->

                    <v-divider v-if="!radios"></v-divider>

                    <!-- Lectures Serching Result -->

                    <div v-if="!radios || radios == this.radioLectures">

                        <!-- Lectures List Headert -->
                        <v-list-subheader
                            class="font-weight-bold text-white"
                        >
                            Lectures
                        </v-list-subheader>

                        <!-- Lectures not found -->
                        <div
                            v-if="this.searchedLectures.length == 0"
                            class="text-center"
                        >
                            Lectures not found
                        </div>

                        <!-- Lectures List -->
                        <v-list-item
                            v-for="lecture in this.searchedLectures"

                            :key="lecture.id"
                            :value="lecture.id"
                            :title="lecture.title"

                            prepend-icon="mdi-human-male-board"
                            @click="openLectureInNewTab(lecture.conference_id, lecture.id)"
                        >
                        </v-list-item>
                    </div>
                </v-list>

                <!-- Vertical Divider -->

                <v-divider vertical></v-divider>

                <!-- Searching Filter -->

                <div class="px-4 py-2">
                    <span class="text-overline">Filter by site</span>

                    <v-radio-group
                        v-model="radios"
                    >
                        <v-radio label="Conferences" :value="this.radioConferences"></v-radio>
                        <v-radio label="Lectures" :value="this.radioLectures"></v-radio>
                    </v-radio-group>
                </div>
            </div>
        </v-card>
    </v-menu>
</template>


<script>
import { mapGetters } from 'vuex'

export default {
    name: 'app-bar-search-input',

    data: () => ({
        menu: false,
        radios: null,
        timeout: null,

        searchValue: '',
        limit: 3,

        radioConferences: 'conferences',
        radioLectures: 'lectures',
    }),

    computed: {
        ...mapGetters({
            searchedConferences: 'conference/searchedConferences',
            searchedLectures: 'lecture/searchedLectures',
        }),
    },

    methods: {
        search(event) {
            const self = this
            clearTimeout(this.timeout)

            this.searchValue = event
            this.menu = event ? true : false

            this.timeout = setTimeout(() => self.getSearchedData(), 800);
        },

        getSearchedData() {
            if (this.searchValue) {
                this.$store.dispatch('conference/searchConferences', {
                    search: this.searchValue,
                    limit: this.limit,
                })

                this.$store.dispatch('lecture/searchLectures', {
                    search: this.searchValue,
                    limit: this.limit,
                })
            }
        },

        openConferenceInNewTab(conferenceId) {
            const routeData = this.$router.resolve({name: 'conferenceShow', params: { id: conferenceId }});
            window.open(routeData.href, '_blank');
        },

        openLectureInNewTab(conferenceId, lectureId) {
            const routeData = this.$router.resolve({name: 'lectureShow', params: { conference_id: conferenceId, lecture_id: lectureId }});
            window.open(routeData.href, '_blank');
        },
    }
}
</script>


<style scoped>
    .search-field {
        min-width: 400px;
    }
</style>
