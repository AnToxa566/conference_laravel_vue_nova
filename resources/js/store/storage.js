import axios from 'axios'
import moment from 'moment'

export default {
    namespaced: true,

    state: { },

    getters: { },

    mutations: { },

    actions: {
        downloadExportFile({ }, fileName) {
            axios.get(`/api/storage/export/${fileName}/download`, { ...JSON.parse(localStorage.getItem('config')), ...{ responseType: 'blob' }})
                .then(res => {
                    const blob = new Blob([res.data])
                    const fileLink = document.createElement('a')

                    fileLink.href = URL.createObjectURL(blob)
                    fileLink.download = moment(Date.now()).format('HHmmss_') + fileName
                    fileLink.click()
                })
                .catch(err => {
                    console.log(err.response)
                })
        },
    }
}
