import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-zoom-meeting', IndexField)
  app.component('detail-zoom-meeting', DetailField)
  app.component('form-zoom-meeting', FormField)
})
