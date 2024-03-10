import app from 'flarum/forum/app';
import { extend, override } from 'flarum/common/extend';
import HeaderSecondary from 'flarum/forum/components/HeaderSecondary';
import Button from 'flarum/components/Button';
import CheckinModal from './components/CheckinModal';

export default function () {

  
  extend(HeaderSecondary.prototype, 'items', (items) => {
    
    if(app.forum.attribute('checkinPosition') === 1 && app.session.user !== null && app.forum.attribute('allowCheckin') === true){
      items.add('checkin', <CheckinModal state={app.checkins} />, 20);
    }
    
  });
}


