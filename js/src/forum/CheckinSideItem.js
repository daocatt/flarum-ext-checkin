
import { extend, override } from 'flarum/common/extend';
import IndexPage from 'flarum/forum/components/IndexPage';
import Button from 'flarum/components/Button';
import CheckinButton from './components/CheckinButton';

export default function () {

    extend(IndexPage.prototype, 'sidebarItems', function(items) {

      
      let itemName = "forum-checkin";
      
      if(app.forum.attribute('checkinPosition') !== 1  && app.session.user !== null && app.forum.attribute('allowCheckin') === true){

        let allowCheckin = app.session.user.attribute("allowCheckin");

        if(allowCheckin === true){
          items.add(itemName, <CheckinButton state="enabled" />,50);
        }else{
          items.add(itemName, <CheckinButton state="disabled" />,50);
        }
      }

    });

}
