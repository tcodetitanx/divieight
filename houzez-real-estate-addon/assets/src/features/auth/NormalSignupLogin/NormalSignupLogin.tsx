import SignupLogin from '@/admin/components/auth/SignupLogin/SignupLogin';

export default function NormalSignupLogin({
	showSignup = false,
}: NormalSignupLoginProps) {
	return (
		<div
			className={
				'max-w-[600px] m-auto rounded-md bg-white shadow pt-4 pb-4 mt-4'
			}
		>
			<SignupLogin showSignup={showSignup} />
		</div>
	);
}

export interface NormalSignupLoginProps {
	showSignup?: boolean;
}
