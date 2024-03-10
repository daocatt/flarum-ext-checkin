import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';
import Stream from 'flarum/utils/Stream';

export default class checkinFailedModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
  }

  className() {
    return 'CheckinModal Modal--small';
  }

  title() {
    return (<div className="failedTitle">{app.translator.trans('gtdxyz-checkin.forum.failed')}</div>);
  }

  content() {
    //
    return (
      <div className="Modal-body">
        <div className="modalText">{app.translator.trans('gtdxyz-checkin.forum.try-again-later')}</div>
      </div>
    );
  }
}
