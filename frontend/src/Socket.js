import Centrifuge from 'centrifuge'

class Socket {
  centrifuge = null;

  connect(token, user_id) {
    this.centrifuge = new Centrifuge('ws://localhost:8000/connection/websocket')
    this.centrifuge.setToken(token)

    this.subscribe(user_id)
    this.centrifuge.connect()
  }

  subscribe(chanell){
    this.centrifuge.subscribe(chanell, function (message) {
      console.log(message)
    });
  }
}

export default new Socket();
