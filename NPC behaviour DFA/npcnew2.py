class NPC:
    def __init__(self):
        self.initial_state = "Idle_Far"
        self.state = self.initial_state
        self.alive = True

        self.transitions = {
            ("Idle_Far", "see_player"): "Chase_Far",
            ("Idle_Far", "player_near"): "Attack_Near",
            ("Idle_Far", "lose_player"): "Idle_Far",
            ("Idle_Far", "low_health"): "Flee_Far",
            ("Idle_Far", "kill"): "Dead",

            ("Wander_Far", "see_player"): "Chase_Far",
            ("Wander_Far", "player_near"): "Attack_Near",
            ("Wander_Far", "lose_player"): "Wander_Far",
            ("Wander_Far", "low_health"): "Flee_Far",
            ("Wander_Far", "kill"): "Dead",

            ("Chase_Far", "see_player"): "Chase_Far",
            ("Chase_Far", "player_near"): "Attack_Near",
            ("Chase_Far", "lose_player"): "Wander_Far",
            ("Chase_Far", "low_health"): "Flee_Far",
            ("Chase_Far", "kill"): "Dead",

            ("Attack_Near", "see_player"): "Attack_Near",
            ("Attack_Near", "player_near"): "Attack_Near",
            ("Attack_Near", "lose_player"): "Wander_Far",
            ("Attack_Near", "low_health"): "Flee_Far",
            ("Attack_Near", "kill"): "Dead",

            ("Flee_Far", "see_player"): "Flee_Far",
            ("Flee_Far", "player_near"): "Flee_Far",
            ("Flee_Far", "lose_player"): "Wander_Far",
            ("Flee_Far", "low_health"): "Flee_Far",
            ("Flee_Far", "kill"): "Dead",

            ("Dead", "see_player"): "Dead",
            ("Dead", "player_near"): "Dead",
            ("Dead", "lose_player"): "Dead",
            ("Dead", "low_health"): "Dead",
            ("Dead", "kill"): "Dead",
        }

    def reset(self):
        self.state = self.initial_state
        self.alive = True

    def transition(self, input_symbol):
        prev = self.state
        self.state = self.transitions[(self.state, input_symbol)]

        if self.state == "Dead":
            self.alive = False

        return prev, self.state

    def describe(self, prev, curr, input_symbol):
        return f"{prev} --[{input_symbol}]--> {curr}"

def show_menu():
    print("------------------------------------")
    print("[1] Player sees NPC")
    print("[2] Player is near")
    print("[3] Player is lost")
    print("[4] NPC Health is low")
    print("[5] NPC is killed")
    print("[6] Reset NPC")
    print("[0] exit")


def get_input():
    mapping = {
        "1": "see_player",
        "2": "player_near",
        "3": "lose_player",
        "4": "low_health",
        "5": "kill",
    }

    choice = input("Enter: ").strip()

    if choice in mapping:
        return mapping[choice]
    elif choice == "6":
        return "reset"
    elif choice == "0":
        return "exit"
    return None


npc = NPC()
step = 1

print("\nNPC Behaviour Automaton (DFA)")
print("you control the environment of the NPC by providing inputs")

while True:
    show_menu()
    inp = get_input()

    if inp is None:
        continue

    if inp == "exit":
        break

    if inp == "reset":
        npc.reset()
        step = 1
        print("NPC Reset")
        continue

    print(f"\nSTEP {step}")
    prev, curr = npc.transition(inp)
    print(npc.describe(prev, curr, inp))

    if not npc.alive:
        print("NPC entered DEAD state (trap state).")
        break

    step += 1