<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Support\Facades\Response;
use JWTAuth;
use Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class TokenEntrustAbility extends BaseMiddleware
{
    public function handle($request, Closure $next, $roles, $permissions, $validateAll = false)
    {

        if (! $token = $this->auth->setRequest($request)->getToken()) {
            return Response::json(['code' => 201, 'message' => 'Required field token is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);

        }

        try {
            $user = $this->auth->authenticate($token);
            //Log::info("Token", ["token :" => $token, "time" => date('H:m:s')]);

            if (! $user) {

                //Log::error('User not found1.', ['token' => $token,'status_code' =>$user]);
                return Response::json(['code' => 427, 'message' => 'Sorry, We are unable to find you. Please contact the support team.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            //            else {
            //                //if ($user->id != 1) {
            //              //$is_exist = DB::table('user_session')->where('token', $token)->exists();
            //              $is_exist = DB::select('SELECT id
            //                                FROM user_session
            //                                WHERE token = ?', [$token]);
            //              //Log::info('exist session :',['token' => $is_exist]);
            //
            //                if (!$is_exist) {
            //                    //Log::info('session expired data', ['token'=>$token,'id' => $user->id]);
            //                    return Response::json(array('code' => 400, 'message' => 'Your session is expired. Please login.', 'cause' => '', 'data' => json_decode("{}")));
            //                }
            //                //}
            //            }

        } catch (TokenInvalidException $e) {
            return Response::json(['code' => 400, 'message' => 'Invalid token.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (TokenExpiredException $e) {
            try {
                $new_token = JWTAuth::refresh($token);
                //Log::info("Refreshed Token", ["new_token :" => $new_token, "old_token :" => $token, "time" => date('H:m:s')]);

                DB::beginTransaction();
                DB::update('UPDATE user_session
                                SET token = ?
                                WHERE token = ?', [$new_token, $token]);
                DB::commit();

            } catch (TokenExpiredException $e) {
                //Log::debug('TokenExpiredException Can not be Refresh', ['status_code' => $e->getStatusCode()]);

                DB::beginTransaction();
                DB::delete('DELETE FROM user_session WHERE token = ?', [$token]);
                DB::commit();

                return Response::json(['code' => 400, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode('{}')]);
            } catch (TokenBlacklistedException $e) {
                //Log::error('The token has been blacklisted.', ['token' => $token,'status_code' => $e->getStatusCode()]);

                DB::beginTransaction();
                DB::delete('DELETE FROM user_session WHERE token = ?', [$token]);
                DB::commit();

                return Response::json(['code' => 400, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode('{}')]);
            } catch (JWTException $e) {
                return Response::json(['code' => 400, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode('{}')]);
            }

            return Response::json(['code' => 401, 'message' => 'Token expired.', 'cause' => '', 'data' => ['new_token' => $new_token]]);
        } catch (JWTException $e) {
            return Response::json(['code' => 400, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode('{}')]);
        }

        if (! $user) {
            //return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
            //Log::error('User not found.', ['token' => $token,'status_code' =>$user]);

            return Response::json(['code' => 427, 'message' => 'Sorry, We are unable to find you. Please contact the support team.', 'cause' => '', 'data' => json_decode('{}')]);
        }

        if (! $request->user()->ability(explode('|', $roles), explode('|', $permissions), ['validate_all' => $validateAll])) {
            return Response::json(['code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode('{}')]);
            //return $this->respond('tymon.jwt.invalid', 'token_invalid', 401, 'Unauthorized');
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}
