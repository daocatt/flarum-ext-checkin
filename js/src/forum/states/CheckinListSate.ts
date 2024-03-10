import type dayjs from 'dayjs';

export default class CheckinListState {
    constructor(app) {
      this.app = app;
  
      /**
       * Whether or not the list are loading.
       *
       * @type {Boolean}
       */
      this.loading = false;
    }
  
    /**
     * Load list into the application's cache if they haven't already
     * been loaded.
     */
    load() {
      
      // if online cross 24H
      if (this.cache && this.app.session.user.attribute('checkin_sync')) {
        if(dayjs(this.app.session.user.attribute('checkin_sync')).isSame(dayjs().format('YYYY-MM-DD'),'day')){
          return;
        }
      }

      this.loading = true;
      m.redraw();
  
      this.app.store
        .find('checkin/history')
        .then((checkins) => {
          this.app.session.user.pushAttributes({ checkin_sync: dayjs().format('YYYY-MM-DD') });
          this.cache = checkins;
        })
        .catch(() => {})
        .then(() => {
          this.loading = false;
          m.redraw();
        });
    }
  }
  