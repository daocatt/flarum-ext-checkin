import app from 'flarum/forum/app';
import { extend } from 'flarum/extend';

import Button from 'flarum/components/Button';
import Alert from 'flarum/common/components/Alert';

import Model from 'flarum/Model';
import User from 'flarum/models/User';

import CheckinSideItem from './CheckinSideItem';
import CheckinHeader from './CheckinHeader';

import CheckinHistoryPage from './components/CheckinHistoryPage';
import CheckInSuccessModal from './components/CheckinSuccessModal';
import CheckInFailedModal from './components/CheckinFailedModal';

import UserCheckinHistory from "./models/UserCheckinHistory";
import CheckinListState from "./states/CheckinListSate";

export { default as extend } from './extend';

app.initializers.add('gtdxyz-checkin', () => {
  
  app.store.models.UserCheckinHistory = UserCheckinHistory;

  app.routes.UserCheckinRoute = {
    path: '/u/:username/checkin/history',
    component: CheckinHistoryPage,
  };

  app.checkins = new CheckinListState(app);

  User.prototype.canCheckin = Model.attribute('allowCheckin');

  app.checkinClick = function() {
    const checkin_days_count = app.session.user.attribute("checkin_days_count");
    const checkinReward = app.forum.attribute("checkinReward");
    const checkinSuccessType = app.forum.attribute("checkinSuccessType");
    const checkinSuccessText = app.forum.attribute("checkinSuccessText");
    const checkinSuccessRewardText = app.forum.attribute("checkinSuccessRewardText");

    const moneyExtensionExist = app.forum.attribute('gtdxyz-money-plus.moneyname') !== undefined;
    let moneyName = "";
    if(moneyExtensionExist === true){
      moneyName = app.forum.attribute('gtdxyz-money-plus.moneyname') || 'MO';
    }
    
    let error_text = app.translator.trans('gtdxyz-checkin.forum.failed')+" "+app.translator.trans('gtdxyz-checkin.forum.try-again-later');
    app.session.user.save({allowCheckin:false,checkin_days_count:checkin_days_count+1})
    .then(() => {
      if(checkinSuccessType===1){
        let rewardText = "";
        if(checkinSuccessText!==""){
          const checkInSuccessText = checkinSuccessText.replace('[days]', checkin_days_count+1);
          const successTextAlertKey = app.alerts.show({type: 'success'}, checkInSuccessText);
        }

        if(moneyExtensionExist===true && checkinSuccessRewardText!==""){
          rewardText = checkinReward + moneyName;
          const checkInSuccessRewardText = checkinSuccessRewardText.replace('[reward]', rewardText);
          const successRewardTextAlertKey = app.alerts.show({type: 'success'}, checkInSuccessRewardText);
        }
      }else if(checkinSuccessType===2){
        app.modal.show(CheckInSuccessModal);
      }
    })
    .catch((e) => {
      const { errors } = JSON.parse(e.xhr.response);
      if(errors.length > 0) {
        error_text = errors[0]?.detail;
      }
      if(checkinSuccessType===1){
        app.alerts.show({type: 'error'}, error_text);
      }
      if(checkinSuccessType===2){
        app.modal.show(CheckInFailedModal);
      }
    });
  };

  CheckinSideItem();

  CheckinHeader();
  
  
});