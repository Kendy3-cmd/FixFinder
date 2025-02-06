import '../css/styles.css';

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { Link, Head } from "@inertiajs/vue3";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .component("Link", Link)
      .component("Head", Head)
      .use(ZiggyVue)
      .mount(el)
  },
  progress: {
    // The delay after which the progress bar will appear, in milliseconds...
    // delay: 250,

    // The color of the progress bar...
    color: 'blue',

    // Whether to include the default NProgress styles...
    includeCSS: true,

    // Whether the NProgress spinner will be shown...
    showSpinner: true,
  },
})