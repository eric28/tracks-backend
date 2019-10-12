export class EnvUtils {
    static readString(key, defaultValue = undefined) {
        const v = DOTENV[key];
        return v === undefined ? defaultValue : String(v);
    }
    static readInteger(key, defaultValue = undefined) {
        const v = DOTENV[key];
        return v === undefined ? defaultValue : parseInt(v);
    }

    static readBoolean(key, defaultValue = undefined) {
        const v = DOTENV[key];
        return v === undefined ? defaultValue : (v == 'true');
    }
}
