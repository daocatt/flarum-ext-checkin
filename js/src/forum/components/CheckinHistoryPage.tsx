import app from 'flarum/forum/app';
import Page from 'flarum/components/Page';
import CheckinContent from "./CheckinContent";

export default class CheckinHistoryPage extends Page {

    oninit(vnode) {
        super.oninit(vnode);

        app.history.push('UserCheckinRoute');

        app.checkins.load();
    }

    oncreate(vnode) {
      super.oncreate(vnode);
      app.setTitle(app.translator.trans('gtdxyz-checkin.forum.checkin'));
      
    }

    view() {
      return (
        <div>
          <CheckinContent state={app.checkins}></CheckinContent>
        </div>
      );
    }
}
