/// <reference types="vite/client" />
interface ImportMetaEnv {
    readonly VITE_ICE_SERVER:string
    readonly VITE_ENVELOP: "development" | string
    readonly VITE_API_ENDPOINT: string
    readonly VITE_VK_APP_ID: number
}