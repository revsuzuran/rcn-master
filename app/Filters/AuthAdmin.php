<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthAdmin implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do something here

        $currentURIPath = "/{$request->uri->getPath()}";

        // check if the current path is auth path, just return true
        // don't forget to use named routes to simplify the call
        // die($currentURIPath);
        if (in_array($currentURIPath, [route_to('login'), route_to('do_auth')])) {
            return;
        } 

        if(!session()->has('masukAdmin'))
        {
        	return redirect()->to(base_url('login'));
        }
        
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}