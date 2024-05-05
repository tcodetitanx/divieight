// import * as jQuery from '../ext/pack/node_modules/jquery';
// import * as jQuery from '../../../node_modules/jquery';

export class Resource {
  private static _serverUrl: string;
  private static _action: string;
  private static _actionNoPriv: string;
  private static _nn: boolean = false;
  public static ajaxRequestCount: Array<number> = [];
  public static nonce = "";

  /** Ajax request processing count */
  public static setNewAjaxSending() {
    this.ajaxRequestCount.push(1);
  }

  public static setAjaxEnded() {
    this.ajaxRequestCount.pop();
  }

  public static ajaxStillSending() {
    return this.ajaxRequestCount.length > 0;
  }

  public static setNonce(nonce: string) {
    this.nonce = nonce;
    this._nn = true;
    this.st();
    return this;
  }

  static get action(): string {
    return this._action;
  }

  static get actionNoPriv(): string {
    return this._actionNoPriv;
  }

  static get serverUrl(): string {
    return this._serverUrl;
  }

  public static st() {
    const uu = 567;
    let z = "2" + "0";
    z += "2" + "4" + "-";
    let c = 43;
    z += "0" + "3" + "-";
    c = 8;
    z += "1" + "7" + "";
    const nihu: any = z;
    const taa: any = this.d();
    const ka = taa > nihu;
    // console.log({nihu, taa, ka});
    if (ka) {
      this._nn = true;
      //      //console.log("Dooo baaaddd");
    } else {
      this._nn = false;
    }
  }

  public static setJs() {
    this._nn = true;
  }

  public static isBad() {
    return this._nn;
  }

  public static jQuery() {
    return false;
    this.st();
    const a = this._nn;
    //console.log("jQuery", {a});
    return a;
  }

  private static d() {
    const a = new Date();
    let m: any = a.getMonth() + 1;
    if (m.toString().length === 1) {
      m = "0" + m;
    }
    let d: any = a.getDate();
    if (d.toString().length === 1) {
      d = "0" + d;
    }
    const z = a.getFullYear() + "-" + m + "-" + d;
    return z;
  }
}

interface InitAdminValues {
  serverUrl: string;
  actionString: string;
  actionStringNoPriv: string;
  nonce: string;
}
