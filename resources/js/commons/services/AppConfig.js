import {EnvUtils} from "../helpers/EnvUtils";

export class AppConfig {

    static get URL() {
        return EnvUtils.readString('APP_URL', 'https://tracks-wearfollowtrack.herokuapp.com');
    }
}
