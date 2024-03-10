import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';
import Stream from 'flarum/utils/Stream';

export default class checkinSuccessModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
  }

  className() {
    return 'CheckinModal Modal--small';
  }

  title() {
    return (<div className="successTitle">{app.translator.trans('gtdxyz-checkin.forum.success')}</div>);
  }

  content() {
    //
    const checkin_days_count = app.session.user.attribute("checkin_days_count");
    const checkinReward = app.forum.attribute("checkinReward");
    const checkinSuccessText = app.forum.attribute("checkinSuccessText");
    const checkinSuccessRewardText = app.forum.attribute("checkinSuccessRewardText");
    const moneyExtensionExist = app.forum.attribute('gtdxyz-money-plus.moneyname')!==undefined;

    let moneyName = "";
    let rewardText = "";
    let successTextClassName = "CheckinModal hideText";
    let rewardTextClassName = "CheckinModal hideText";

    if(checkinSuccessText!==""){
      successTextClassName = "CheckinModal successText";
    }

    if(moneyExtensionExist===true && checkinSuccessRewardText!==""){
      moneyName = app.forum.attribute('gtdxyz-money-plus.moneyname') || 'MO';
      rewardText = checkinReward + moneyName;
      rewardTextClassName = "CheckinModal rewardText";
    }

    return (
      <div className="Modal-body">
        <div className={successTextClassName}>{checkinSuccessText.replace('[days]', checkin_days_count+1)}</div>
        <div className={rewardTextClassName}>{checkinSuccessRewardText.replace('[reward]', rewardText)}</div>
      </div>
    );
  }
}
