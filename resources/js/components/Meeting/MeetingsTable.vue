<template>
    <div v-if="this.meetings.length">
        <v-table>
            <thead>
                <tr>
                    <th class="text-left">
                        Lecture's title
                    </th>
                    <th class="text-left">
                        uuid
                    </th>
                    <th class="text-left">
                        id
                    </th>
                    <th class="text-left">
                        host_id
                    </th>
                    <th class="text-left">
                        topic
                    </th>
                    <th class="text-left">
                        type
                    </th>
                    <th class="text-left">
                        start_time
                    </th>
                    <th class="text-left">
                        timezone
                    </th>
                    <th class="text-left">
                        created_at
                    </th>
                    <th class="text-left">
                        join_url
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="meeting in paginatedSortedMeetings"
                    :key="meeting.id"
                >
                    <td>{{ meeting.topic }}</td>
                    <td>{{ meeting.uuid }}</td>
                    <td>{{ meeting.id }}</td>
                    <td>{{ meeting.host_id }}</td>
                    <td>{{ meeting.topic }}</td>
                    <td>{{ meeting.type }}</td>
                    <td>{{ meeting.start_time }}</td>
                    <td>{{ meeting.timezone }}</td>
                    <td>{{ meeting.created_at }}</td>
                    <td>{{ meeting.join_url }}</td>
                </tr>
            </tbody>
        </v-table>

        <v-pagination
            v-model="currentPage"
            :length="paginationLength"
            @update:modelValue="getResults"
        ></v-pagination>
    </div>

    <div
        v-else
        class="text-subtitle-1 font-weight-medium text-center"
    >
        Meetings not found
    </div>
</template>


<script>
export default {
    data: () => ({
        currentPage: 1,
        perPage: 10,
    }),

    computed: {
        paginatedSortedMeetings() {
            return this.sortMeetings.slice((this.currentPage - 1) * this.perPage, this.currentPage * this.perPage)
        },

        sortMeetings() {
            return this.meetings.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        },

        meetings() {
            return this.$store.getters['meeting/meetingsOfExistingLectures']
        },

        paginationLength() {
            return Math.ceil(this.meetings.length / this.perPage)
        },
    },

    methods: {
        getResults(page) {
            this.currentPage = page
        }
    },
}
</script>
