import app from 'flarum/forum/app';
import Component, { ComponentAttrs } from 'flarum/common/Component';
import type dayjs from 'dayjs';
import Button from 'flarum/common/components/Button';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';

import icon from 'flarum/common/helpers/icon';

export default class CheckinButton extends Component {
  oninit(vnode) {
    super.oninit(vnode);

    this.state = this.attrs.state;
    
  }

  view() {
    const btn = this.state;

    let checkinButtonText = app.translator.trans('gtdxyz-checkin.forum.checkin');
    const checkin_days_count = app.session.user.attribute("checkin_days_count");

    if(btn === 'enabled'){
        
        return (
        
            Button.component({
                icon: 'far fa-check-circle',
                className: 'Button CheckinButton CheckinButton--active',
                id:"checkinButton",
                onclick: app.checkinClick,
              }, checkinButtonText)
              
        );

    } else {
        checkinButtonText = app.translator.trans('gtdxyz-checkin.forum.checked-days', {count: checkin_days_count});
        return (
            Button.component({
                icon: 'fas fa-check-square',
                className: 'Button CheckinButton CheckinButton--checked',
                disabled: true
              }, checkinButtonText)
        );
    }
    
  }
}
