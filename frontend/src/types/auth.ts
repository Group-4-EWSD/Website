export interface Credentials {
    email: string;
    password: string;
  }
  
export interface RegisterData extends Credentials {
    name: string;
    confirmPassword: string;
}