<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpseclib3\Net\SSH2;

class BrandingController extends Controller
{
    public function changeBrand(Request $request)
    {
        set_time_limit(0);

        $data = $request->validate([
            'ip' => 'required|ip'
        ]);

        $ip = $data['ip'];
        $user = "coelhovps";
        $password = "Coelho12";

        $ssh = null;

        while (true) {
            try {
                $connection = new SSH2($ip, 22, 10);
                if ($connection->login($user, $password)) {
                    $ssh = $connection;
                    break;
                }
            } catch (\Exception $e) {
            }
            sleep(5);
        }

        $motd = <<<'EOT'
####################################################
#                                                  #
#               CoelhoVPS - Bem-vindo              #
#         Servidor gerenciado pela CoelhoVPS       #
#                                                  #
####################################################
EOT;

        $commands = [
            'sudo hostnamectl set-hostname CoelhoVPS || sudo hostname CoelhoVPS',
            "echo " . escapeshellarg($motd) . " | sudo tee /etc/motd > /dev/null",
            "echo " . escapeshellarg("CoelhoVPS - Acesso autorizado somente para administradores.\n") . " | sudo tee /etc/issue > /dev/null",
            "echo " . escapeshellarg("CoelhoVPS - Acesso autorizado somente para administradores.\n") . " | sudo tee /etc/issue.net > /dev/null",
            "echo " . escapeshellarg("CoelhoVPS - Conexão autorizada.\n") . " | sudo tee /etc/ssh/coelhovps_banner > /dev/null",
            "sudo sed -i '/Banner/d' /etc/ssh/sshd_config",
            "echo 'Banner /etc/ssh/coelhovps_banner' | sudo tee -a /etc/ssh/sshd_config > /dev/null",
            "echo 'export PS1=\"\\[\\e[1;32m\\]CoelhoVPS:\\w\\$ \\[\\e[0m\\]\"' | sudo tee -a /root/.bashrc > /dev/null",
            "echo 'export PS1=\"\\[\\e[1;32m\\]CoelhoVPS:\\w\\$ \\[\\e[0m\\]\"' | sudo tee -a /home/coelhovps/.bashrc > /dev/null",
            "sudo systemctl restart ssh || sudo systemctl restart sshd || sudo service ssh restart"
        ];

        $result = [];

        foreach ($commands as $cmd) {
            $result[] = [
                'cmd' => $cmd,
                'output' => trim($ssh->exec($cmd))
            ];
        }

        return response()->json([
            'status' => 'ok',
            'ip' => $ip,
            'actions' => $result
        ]);
    }
}
