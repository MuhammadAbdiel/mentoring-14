<?php

/* Table structure for table `products` */
// CREATE TABLE `products` (
//   `id` int(10) UNSIGNED NOT NULL,
//   `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
//   `price` double NOT NULL,
//   `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
//   `updated_at` datetime DEFAULT NULL
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
// ALTER TABLE `products` ADD PRIMARY KEY (`id`);
// ALTER TABLE `products` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1; COMMIT;

/**
 * Product class.
 * 
 * @extends REST_Controller
 */
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Products extends REST_Controller
{

    /**
     * CONSTRUCTOR | LOAD MODEL
     *
     * @return Response
     */

    private $decodedToken;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('Product_model');

        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken();
            if ($decodedToken['status']) {
                // ------- Main Logic part -------
                $this->decodedToken = $decodedToken;
                // ------------- End -------------
            } else {
                $this->response($decodedToken);
            }
        } else {
            $this->response(['Authentication failed'], REST_Controller::HTTP_OK);
        }
    }

    /**
     * SHOW | GET method.
     *
     * @return Response
     */
    public function index_get(?string $id = null)
    {
        if (!empty($id)) {
            $products = $this->Product_model->show($id);
            if (empty($products)) {
                $this->response([
                    'message' => "Product with id $id is no found"
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $products = $this->Product_model->show();
        }
        $this->response([
            "message" => "Success Get Products",
            "data" => $products
        ], REST_Controller::HTTP_OK);
    }

    /**
     * INSERT | POST method.
     *
     * @return Response
     */
    public function index_post()
    {
        $this->form_validation->reset_validation();
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules($this->Product_model->rules());
        if ($this->form_validation->run() == FALSE) {
            $this->response([
                "message" => "Failed to create the product",
                "errors" => $this->form_validation->error_array()
            ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $record = $this->Product_model->insert($this->post());
            $this->response([
                "message" => "Success Create the Product",
                "data" => $record,
            ], REST_Controller::HTTP_CREATED);
        }
    }

    /**
     * UPDATE | PUT method.
     *
     * @return Response
     */
    public function update_post($id)
    {
        $this->form_validation->reset_validation();
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules($this->Product_model->rules());

        if ($this->form_validation->run() == FALSE) {
            $this->response([
                "message" => "Failed to update the product",
                "errors" => $this->form_validation->error_array()
            ], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            if ($this->Product_model->show($id)) {
                $this->Product_model->update($this->post(), $id);
                return $this->response([
                    "message" => "Success Update the Product",
                ], REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    "message" => "Product with id $id, is not found"
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * DELETE method.
     *
     * @return Response
     */
    public function delete_post($id)
    {
        if ($this->Product_model->delete($id)) {
            $this->response([
                "message" => "Success Delete the Product"
            ]);
        } else {
            $this->response([
                "message" => "Failed to delete the Product , product not found"
            ]);
        }
    }
}
